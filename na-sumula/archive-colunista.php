<?php
/** Arquivo de Colunistas — Mundo da Copa. */
get_header();
$colunistas = get_posts( array( 'post_type'=>'colunista', 'post_status'=>'publish', 'posts_per_page'=>-1, 'orderby'=>'title', 'order'=>'ASC', 'no_found_rows'=>true ) );
?>
<section class="mdc-archive-hero">
  <div class="mdc-container"><span class="mdc-section-kicker">OPINIÃO</span><h1>Colunistas</h1><p>Vozes que ajudam a olhar para o futebol por outros ângulos.</p></div>
</section>
<section class="mdc-section mdc-section--archive-results mdc-colunistas-archive">
  <div class="mdc-container">
    <?php if ( $colunistas ) : ?>
      <div class="mdc-colunistas-grid">
      <?php foreach ( $colunistas as $colunista_post ) : $d = mdc_dados_colunista( $colunista_post->ID ); ?>
        <article class="mdc-colunista-card">
          <a class="mdc-colunista-card__media" href="<?php echo esc_url( $d['url'] ); ?>"><?php if ( has_post_thumbnail( $colunista_post->ID ) ) { echo get_the_post_thumbnail( $colunista_post->ID, 'large', array('loading'=>'lazy','alt'=>esc_attr($d['nome'])) ); } else { ?><span aria-hidden="true"><?php echo esc_html( mb_strtoupper( mb_substr( $d['nome'], 0, 1 ) ) ); ?></span><?php } ?></a>
          <div class="mdc-colunista-card__body"><span class="mdc-section-kicker">COLUNA</span><?php if($d['coluna']) : ?><h2><a href="<?php echo esc_url( $d['url'] ); ?>"><?php echo esc_html($d['coluna']); ?></a></h2><?php endif; ?><strong class="mdc-colunista-card__author"><?php echo esc_html($d['nome']); ?></strong><?php if($d['cargo']) : ?><p><?php echo esc_html($d['cargo']); ?></p><?php endif; ?><?php if($d['bio']) : ?><p><?php echo esc_html(wp_trim_words($d['bio'],24)); ?></p><?php endif; ?><a class="mdc-text-link" href="<?php echo esc_url($d['url']); ?>">Ver perfil <span>→</span></a></div>
        </article>
      <?php endforeach; ?>
      </div>
    <?php else : ?><div class="mdc-empty-state"><strong>Nenhum colunista publicado.</strong><span>Cadastre os primeiros nomes da equipe editorial.</span></div><?php endif; ?>
  </div>
</section>
<?php get_footer(); ?>
