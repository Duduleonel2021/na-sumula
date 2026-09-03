<?php
/**
 * Mundo da Copa — Sistema editorial de atualização ao vivo.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function mdc_atualizacao_ativa( $post_id = 0 ) {
    $post_id = $post_id ? absint( $post_id ) : get_the_ID();

    if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
        return false;
    }

    $valor = get_post_meta( $post_id, 'mdc_em_atualizacao', true );

    if ( is_bool( $valor ) ) {
        return $valor;
    }

    $valor = strtolower( trim( (string) $valor ) );

    return in_array( $valor, array( '1', 'true', 'yes', 'sim', 'on', 'ativo', 'active' ), true );
}

function mdc_atualizacoes( $post_id = 0 ) {
    $post_id = $post_id ? absint( $post_id ) : get_the_ID();
    $raw = get_post_meta( $post_id, 'mdc_atualizacoes', true );
    if ( ! $raw ) { return array(); }
    $lines = preg_split( '/\r\n|\r|\n/', (string) $raw );
    $items = array();
    foreach ( $lines as $line ) {
        $line = trim( wp_strip_all_tags( $line ) );
        if ( '' === $line ) { continue; }
        $parts = explode( '|', $line, 2 );
        $time = trim( $parts[0] );
        $text = isset( $parts[1] ) ? trim( $parts[1] ) : trim( $parts[0] );
        if ( isset( $parts[1] ) && preg_match( '/^\d{1,2}:\d{2}$/', $time ) ) {
            $items[] = array( 'hora' => $time, 'texto' => $text );
        } else {
            $items[] = array( 'hora' => '', 'texto' => $text );
        }
    }
    return $items;
}

function mdc_atualizacao_ultima_hora( $post_id = 0 ) {
    $items = mdc_atualizacoes( $post_id );
    return ! empty( $items[0]['hora'] ) ? $items[0]['hora'] : '';
}

function mdc_render_atualizacao( $post_id = 0 ) {
    $post_id = $post_id ? absint( $post_id ) : get_the_ID();
    if ( ! mdc_atualizacao_ativa( $post_id ) ) { return ''; }

    $items = mdc_atualizacoes( $post_id );
    $last = mdc_atualizacao_ultima_hora( $post_id );
    $modified = get_the_modified_time( 'c', $post_id );

    ob_start(); ?>
    <section
        class="mdc-live"
        data-mdc-live
        data-post-id="<?php echo esc_attr( $post_id ); ?>"
        data-nonce="<?php echo esc_attr( wp_create_nonce( 'mdc_atualizacao_' . $post_id ) ); ?>"
        data-modified="<?php echo esc_attr( $modified ); ?>"
        aria-label="Atualizações ao vivo"
    >
        <div class="mdc-live__head">
            <div class="mdc-live__title">
                <span class="mdc-live__dot" aria-hidden="true"></span>
                <strong>Atualizações ao vivo</strong>
            </div>
            <div class="mdc-live__controls">
                <span class="mdc-live__status" data-live-status>
                    Em atualização<?php if ( $last ) : ?> · última <?php echo esc_html( $last ); ?><?php endif; ?>
                </span>
                <label class="mdc-live__auto">
                    <span>Atualizações automáticas</span>
                    <input type="checkbox" data-live-toggle aria-label="Ativar atualizações automáticas">
                    <span class="mdc-live__switch" aria-hidden="true"></span>
                </label>
            </div>
        </div>

        <div class="mdc-live__content" data-live-content>
            <?php if ( $items ) : ?>
                <div class="mdc-live__timeline">
                    <?php foreach ( $items as $item ) : ?>
                        <article class="mdc-live__item">
                            <?php if ( $item['hora'] ) : ?><time><?php echo esc_html( $item['hora'] ); ?></time><?php else : ?><span aria-hidden="true"></span><?php endif; ?>
                            <div class="mdc-live__entry">
                                <strong><?php echo esc_html( $item['hora'] ? 'Atualização' : 'Nota' ); ?></strong>
                                <p><?php echo esc_html( $item['texto'] ); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="mdc-live__empty">Esta matéria está sendo atualizada pela redação.</p>
            <?php endif; ?>
        </div>
    </section>
    <?php return ob_get_clean();
}

/**
 * Feed público das atualizações ao vivo.
 *
 * O conteúdo continua sendo controlado pelo WordPress; o navegador apenas
 * consulta se a matéria foi modificada e recebe a versão mais recente.
 */
function mdc_atualizacao_feed_ajax() {
    $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
    $nonce   = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

    if (
        ! $post_id ||
        ! wp_verify_nonce( $nonce, 'mdc_atualizacao_' . $post_id ) ||
        'post' !== get_post_type( $post_id ) ||
        'publish' !== get_post_status( $post_id ) ||
        ! mdc_atualizacao_ativa( $post_id )
    ) {
        wp_send_json_error( array( 'message' => 'Atualização indisponível.' ), 400 );
    }

    wp_send_json_success(
        array(
            'html'     => mdc_render_atualizacao( $post_id ),
            'modified' => get_the_modified_time( 'c', $post_id ),
        )
    );
}
add_action( 'wp_ajax_mdc_atualizacao_feed', 'mdc_atualizacao_feed_ajax' );
add_action( 'wp_ajax_nopriv_mdc_atualizacao_feed', 'mdc_atualizacao_feed_ajax' );
