<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Campos nativos do WordPress.
 * Sem ACF: dados armazenados em post_meta.
 */
function mdc_field_groups() {
    return array(
        'copa' => array(
            'Dados da Copa' => array(
                'mdc_ano' => array( 'label'=>'Ano', 'type'=>'number' ),
                'mdc_sedes' => array( 'label'=>'Países e cidades-sede', 'type'=>'textarea' ),
                'mdc_data_inicio' => array( 'label'=>'Data de início', 'type'=>'date' ),
                'mdc_data_fim' => array( 'label'=>'Data de encerramento', 'type'=>'date' ),
                'mdc_campeao' => array( 'label'=>'Campeão', 'type'=>'post_select', 'post_type'=>'selecao' ),
                'mdc_vice' => array( 'label'=>'Vice-campeão', 'type'=>'post_select', 'post_type'=>'selecao' ),
                'mdc_terceiro' => array( 'label'=>'3º colocado', 'type'=>'post_select', 'post_type'=>'selecao' ),
                'mdc_quarto' => array( 'label'=>'4º colocado', 'type'=>'post_select', 'post_type'=>'selecao' ),
                'mdc_num_selecoes' => array( 'label'=>'Número de seleções', 'type'=>'number' ),
                'mdc_num_jogos' => array( 'label'=>'Número de jogos', 'type'=>'number' ),
                'mdc_num_gols' => array( 'label'=>'Número de gols', 'type'=>'number' ),
                'mdc_artilheiro' => array( 'label'=>'Artilheiro', 'type'=>'post_select', 'post_type'=>'jogador' ),
                'mdc_gols_artilheiro' => array( 'label'=>'Gols do artilheiro', 'type'=>'number' ),
                'mdc_selecoes_participantes' => array( 'label'=>'Seleções participantes', 'type'=>'post_select_multi', 'post_type'=>'selecao' ),
                'mdc_jogadores_destaque' => array( 'label'=>'Jogadores em destaque', 'type'=>'post_select_multi', 'post_type'=>'jogador' ),
                'mdc_estadios' => array( 'label'=>'Estádios', 'type'=>'post_select_multi', 'post_type'=>'estadio' ),
                'mdc_reportagens_relacionadas' => array( 'label'=>'Reportagens relacionadas', 'type'=>'post_select_multi', 'post_type'=>'post' ),
                'mdc_historia' => array( 'label'=>'A edição', 'type'=>'textarea' ),
                'mdc_campanha' => array( 'label'=>'Campanha do campeão', 'type'=>'textarea' ),
                'mdc_final' => array( 'label'=>'A final', 'type'=>'textarea' ),
                'mdc_curiosidades' => array( 'label'=>'Curiosidades', 'type'=>'textarea' ),
            ),
            'Mídia' => array(
                'mdc_capa' => array( 'label'=>'Imagem da moldura do Hero', 'type'=>'imagem', 'ajuda'=>'Imagem específica exibida dentro da moldura do Hero. Prefira uma imagem vertical. A imagem destacada do WordPress é usada separadamente como fundo do Hero.' ),
                'mdc_galeria' => array( 'label'=>'Galeria de imagens', 'type'=>'galeria' ),
                'mdc_videos' => array( 'label'=>'Vídeos', 'type'=>'videos' ),
            ),
        ),
        'selecao' => array(
            'Dados da Seleção' => array(
                'mdc_sigla' => array( 'label'=>'Sigla', 'type'=>'text' ),
                'mdc_titulos' => array( 'label'=>'Títulos mundiais', 'type'=>'number' ),
                'mdc_participacoes' => array( 'label'=>'Participações em Copas', 'type'=>'number' ),
                'mdc_melhor_campanha' => array( 'label'=>'Melhor campanha', 'type'=>'text' ),
                'mdc_alcunhas' => array( 'label'=>'Alcunhas', 'type'=>'text' ),
                'mdc_federacao' => array( 'label'=>'Entidade nacional', 'type'=>'post_select', 'post_type'=>'entidade' ),
                'mdc_confederacao' => array( 'label'=>'Confederação continental', 'type'=>'post_select', 'post_type'=>'entidade' ),
                'mdc_marca_uniforme' => array( 'label'=>'Marca do uniforme', 'type'=>'text' ),
                'mdc_treinador' => array( 'label'=>'Treinador', 'type'=>'text' ),
                'mdc_capitao' => array( 'label'=>'Capitão', 'type'=>'text' ),
                'mdc_jogador_mais_participacoes' => array( 'label'=>'Jogador com mais participações', 'type'=>'text' ),
                'mdc_maior_artilheiro' => array( 'label'=>'Maior artilheiro', 'type'=>'text' ),
                'mdc_principais_titulos' => array( 'label'=>'Principais títulos', 'type'=>'textarea' ),
                'mdc_ranking' => array( 'label'=>'Ranking atual da FIFA', 'type'=>'number' ),
                'mdc_copas' => array( 'label'=>'Copas disputadas', 'type'=>'post_select_multi', 'post_type'=>'copa' ),
                'mdc_jogadores_destaque' => array( 'label'=>'Jogadores de destaque', 'type'=>'post_select_multi', 'post_type'=>'jogador' ),
                'mdc_reportagens_relacionadas' => array( 'label'=>'Reportagens relacionadas', 'type'=>'post_select_multi', 'post_type'=>'post' ),
                'mdc_historia' => array( 'label'=>'História', 'type'=>'textarea' ),
            ),
            'Dados atuais' => array(
                'mdc_presidente' => array( 'label'=>'Presidente da federação', 'type'=>'text' ),
                'mdc_atualizado' => array( 'label'=>'Dados atualizados em', 'type'=>'date', 'ajuda'=>'Exibido como legenda sob os dados atuais.' ),
            ),
            'Localização' => array(
                'mdc_iso' => array( 'label'=>'Código do país (ISO 3166-1 alfa-2)', 'type'=>'text', 'ajuda'=>'Duas letras: BR, AR, FR. Destaca o país no mapa.' ),
                'mdc_latitude' => array( 'label'=>'Latitude', 'type'=>'text', 'ajuda'=>'Ex.: -14.24' ),
                'mdc_longitude' => array( 'label'=>'Longitude', 'type'=>'text', 'ajuda'=>'Ex.: -51.93' ),
            ),
            'Mídia' => array(
                'mdc_bandeira' => array( 'label'=>'Bandeira', 'type'=>'imagem', 'ajuda'=>'A imagem destacada continua sendo o escudo. Esta é a bandeira do país.' ),
                'mdc_capa' => array( 'label'=>'Imagem da moldura do Hero', 'type'=>'imagem', 'ajuda'=>'Imagem específica exibida dentro da moldura do Hero. A imagem destacada do WordPress é usada separadamente como fundo.' ),
                'mdc_galeria' => array( 'label'=>'Galeria de imagens', 'type'=>'galeria' ),
                'mdc_videos' => array( 'label'=>'Vídeos', 'type'=>'videos' ),
            ),
        ),
        'jogador' => array(
            'Dados do Jogador' => array(
                'mdc_nome_completo' => array( 'label'=>'Nome completo', 'type'=>'text' ),
                'mdc_selecao' => array( 'label'=>'Seleção', 'type'=>'post_select', 'post_type'=>'selecao' ),
                'mdc_nacionalidade' => array( 'label'=>'Nacionalidade', 'type'=>'text' ),
                'mdc_data_nascimento' => array( 'label'=>'Data de nascimento', 'type'=>'date' ),
                'mdc_copas_disputadas' => array( 'label'=>'Copas disputadas', 'type'=>'number' ),
                'mdc_jogos_copas' => array( 'label'=>'Jogos em Copas', 'type'=>'number' ),
                'mdc_gols_copas' => array( 'label'=>'Gols em Copas', 'type'=>'number' ),
                'mdc_titulos' => array( 'label'=>'Títulos', 'type'=>'number' ),
                'mdc_copas_relacionadas' => array( 'label'=>'Copas relacionadas', 'type'=>'post_select_multi', 'post_type'=>'copa' ),
                'mdc_reportagens_relacionadas' => array( 'label'=>'Reportagens relacionadas', 'type'=>'post_select_multi', 'post_type'=>'post' ),
                'mdc_biografia' => array( 'label'=>'Biografia', 'type'=>'textarea' ),
                'mdc_momentos' => array( 'label'=>'Principais momentos', 'type'=>'textarea' ),
                'mdc_curiosidades' => array( 'label'=>'Curiosidades', 'type'=>'textarea' ),
            ),
            'Ficha pessoal' => array(
                'mdc_status' => array( 'label'=>'Situação', 'type'=>'select', 'options'=>array( 'ativo'=>'Em atividade', 'aposentado'=>'Aposentado', 'falecido'=>'Falecido' ) ),
                'mdc_local_nascimento' => array( 'label'=>'Local de nascimento', 'type'=>'text', 'ajuda'=>'Cidade, estado, país.' ),
                'mdc_data_morte' => array( 'label'=>'Data de morte', 'type'=>'date' ),
                'mdc_local_morte' => array( 'label'=>'Local de morte', 'type'=>'text' ),
                'mdc_causa_morte' => array( 'label'=>'Causa da morte', 'type'=>'text' ),
                'mdc_altura' => array( 'label'=>'Altura', 'type'=>'text', 'ajuda'=>'Ex.: 1,73 m' ),
                'mdc_peso' => array( 'label'=>'Peso', 'type'=>'text', 'ajuda'=>'Ex.: 70 kg' ),
                'mdc_pe' => array( 'label'=>'Pé', 'type'=>'select', 'options'=>array( 'destro'=>'Destro', 'canhoto'=>'Canhoto', 'ambidestro'=>'Ambidestro' ) ),
                'mdc_apelido' => array( 'label'=>'Apelido', 'type'=>'text' ),
                'mdc_clubes' => array( 'label'=>'Clubes', 'type'=>'textarea', 'ajuda'=>'Um por linha. Ex.: Santos (1956–1974)' ),
            ),
            'Estatísticas de carreira' => array(
                'mdc_clube_atual' => array( 'label'=>'Clube atual', 'type'=>'text' ),
                'mdc_jogos_carreira' => array( 'label'=>'Jogos na carreira', 'type'=>'number' ),
                'mdc_gols_carreira' => array( 'label'=>'Gols na carreira', 'type'=>'number' ),
                'mdc_titulos_carreira' => array( 'label'=>'Títulos na carreira', 'type'=>'number' ),
                'mdc_atualizado' => array( 'label'=>'Estatísticas atualizadas em', 'type'=>'date', 'ajuda'=>'Exibido como legenda sob os números.' ),
            ),
            'Mídia' => array(
                'mdc_capa' => array( 'label'=>'Imagem da moldura do Hero', 'type'=>'imagem', 'ajuda'=>'Imagem específica exibida dentro da moldura do Hero. A imagem destacada do WordPress é usada separadamente como fundo.' ),
                'mdc_galeria' => array( 'label'=>'Galeria de imagens', 'type'=>'galeria' ),
                'mdc_videos' => array( 'label'=>'Vídeos', 'type'=>'videos' ),
            ),
        ),
        'estadio' => array(
            'Dados do Estádio' => array(
                'mdc_cidade' => array( 'label'=>'Cidade', 'type'=>'text' ),
                'mdc_capacidade' => array( 'label'=>'Capacidade', 'type'=>'number' ),
                'mdc_copas' => array( 'label'=>'Copas relacionadas', 'type'=>'post_select_multi', 'post_type'=>'copa' ),
                'mdc_descricao' => array( 'label'=>'Descrição', 'type'=>'textarea' ),
                'mdc_curiosidades' => array( 'label'=>'Curiosidades', 'type'=>'textarea' ),
            ),
            'Mídia' => array(
                'mdc_capa' => array( 'label'=>'Imagem da moldura do Hero', 'type'=>'imagem', 'ajuda'=>'Imagem específica exibida dentro da moldura do Hero. A imagem destacada do WordPress é usada separadamente como fundo.' ),
                'mdc_galeria' => array( 'label'=>'Galeria de imagens', 'type'=>'galeria' ),
                'mdc_videos' => array( 'label'=>'Vídeos', 'type'=>'videos' ),
            ),
        ),
        'entidade' => array(
            'Identidade da entidade' => array(
                'mdc_nivel_entidade' => array( 'label'=>'Nível da entidade', 'type'=>'select', 'options'=>array(
                    'mundial'=>'Mundial',
                    'continental'=>'Continental',
                    'nacional'=>'Nacional',
                ), 'ajuda'=>'Mundial: FIFA. Continental: CONMEBOL, UEFA etc. Nacional: CBF, AFA etc.' ),
                'mdc_sigla' => array( 'label'=>'Sigla', 'type'=>'text' ),
                'mdc_nome_oficial' => array( 'label'=>'Nome oficial', 'type'=>'text' ),
                'mdc_regiao' => array( 'label'=>'Região / continente', 'type'=>'text' ),
                'mdc_site' => array( 'label'=>'Site oficial', 'type'=>'url' ),
                'mdc_historia' => array( 'label'=>'História', 'type'=>'textarea' ),
            ),
            'Dados institucionais' => array(
                'mdc_fundacao' => array( 'label'=>'Fundação', 'type'=>'date' ),
                'mdc_sede' => array( 'label'=>'Sede', 'type'=>'text', 'ajuda'=>'Cidade e país.' ),
                'mdc_endereco' => array( 'label'=>'Endereço', 'type'=>'textarea' ),
                'mdc_presidente' => array( 'label'=>'Presidente', 'type'=>'text' ),
                'mdc_membros' => array( 'label'=>'Federações filiadas', 'type'=>'number' ),
                'mdc_treinador' => array( 'label'=>'Treinador', 'type'=>'text' ),
                'mdc_filiacao_fifa' => array( 'label'=>'Filiação à FIFA', 'type'=>'text' ),
                'mdc_filiacao_conmebol' => array( 'label'=>'Filiação à CONMEBOL', 'type'=>'text' ),
                'mdc_campeonatos' => array( 'label'=>'Competições / campeonatos que organiza', 'type'=>'textarea', 'ajuda'=>'Uma por linha.' ),
                'mdc_atualizado' => array( 'label'=>'Dados atualizados em', 'type'=>'date' ),
            ),
            'Localização' => array(
                'mdc_iso' => array( 'label'=>'Código do país (ISO 3166-1 alfa-2)', 'type'=>'text' ),
            ),
            'Redes sociais' => array(
                'mdc_redes' => array( 'label'=>'Perfis', 'type'=>'textarea', 'ajuda'=>'Um por linha: Instagram|https://instagram.com/...' ),
            ),
            'Mídia' => array(
                'mdc_capa' => array( 'label'=>'Imagem da moldura do Hero', 'type'=>'imagem' ),
                'mdc_galeria' => array( 'label'=>'Galeria de imagens', 'type'=>'galeria' ),
                'mdc_videos' => array( 'label'=>'Vídeos', 'type'=>'videos' ),
            ),
        ),
        'colunista' => array(
            'Identidade editorial' => array(
                'mdc_colunista_coluna' => array( 'label'=>'Nome da coluna', 'type'=>'text', 'ajuda'=>'Ex.: Copa do Avesso. É o nome editorial exibido na home e nas páginas de colunistas.' ),
                'mdc_colunista_cargo' => array( 'label'=>'Cargo / especialidade', 'type'=>'text', 'ajuda'=>'Ex.: Jornalista, historiador, comentarista.' ),
                'mdc_colunista_bio' => array( 'label'=>'Bio curta', 'type'=>'textarea', 'ajuda'=>'Resumo profissional exibido no perfil.' ),
            ),
            'Redes sociais e site' => array(
                'mdc_colunista_instagram' => array( 'label'=>'Instagram', 'type'=>'url' ),
                'mdc_colunista_x' => array( 'label'=>'X', 'type'=>'url' ),
                'mdc_colunista_facebook' => array( 'label'=>'Facebook', 'type'=>'url' ),
                'mdc_colunista_linkedin' => array( 'label'=>'LinkedIn', 'type'=>'url' ),
                'mdc_colunista_site' => array( 'label'=>'Site pessoal', 'type'=>'url' ),
            ),
            'Exibição' => array(
                'mdc_colunista_destaque' => array( 'label'=>'Destacar na página inicial', 'type'=>'bool' ),
            ),
        ),
        'post' => array(
            'Dados editoriais' => array(
                'mdc_formato_post' => array( 'label'=>'Formato da publicação', 'type'=>'select', 'options'=>array(
                    'padrao'=>'Padrão',
                    'video'=>'Vídeo',
                    'audio'=>'Áudio',
                    'ranking'=>'Ranking',
                    'interativo'=>'Interativo',
                ), 'ajuda'=>'Padrão usa a imagem destacada. Vídeo e Áudio usam o endereço informado na seção Mídia. Ranking exibe os itens cadastrados no bloco de Ranking. Interativo abre o sistema de Quiz ou Enquete.' ),
                'mdc_subtitulo' => array( 'label'=>'Subtítulo (linha fina)', 'type'=>'textarea' ),
                'mdc_em_atualizacao' => array( 'label'=>'Conteúdo em atualização', 'type'=>'bool', 'ajuda'=>'Marque esta opção enquanto a matéria estiver sendo atualizada. O portal exibirá automaticamente o indicador de atualização no topo da matéria, na imagem de destaque e nos cards.' ),
                'mdc_atualizacoes' => array( 'label'=>'Atualizações da matéria', 'type'=>'textarea', 'ajuda'=>'Uma atualização por linha no formato HH:MM|Texto da atualização. Ex.: 19:42|Brasil confirma a escalação. A mais recente deve ficar no topo.' ),
                'mdc_colunista' => array( 'label'=>'Colunista', 'type'=>'post_select', 'post_type'=>'colunista', 'ajuda'=>'Opcional. Vincula a reportagem a um colunista cadastrado.' ),
                'mdc_fonte_post' => array( 'label'=>'Fonte do post', 'type'=>'text', 'ajuda'=>'Ex.: Agência Brasil, FIFA, Reuters.' ),
                'mdc_fonte_url' => array( 'label'=>'URL da fonte', 'type'=>'url' ),
                'mdc_patrocinado' => array( 'label'=>'Conteúdo patrocinado', 'type'=>'bool' ),
                'mdc_patrocinador_nome' => array( 'label'=>'Nome do patrocinador', 'type'=>'text' ),
                'mdc_patrocinador_url' => array( 'label'=>'Link do patrocinador', 'type'=>'url' ),
                'mdc_patrocinador_logo' => array( 'label'=>'Logo do patrocinador', 'type'=>'imagem' ),
                'mdc_legenda_imagem' => array( 'label'=>'Legenda da imagem destacada', 'type'=>'text' ),
                'mdc_copa_relacionada' => array( 'label'=>'Copa relacionada', 'type'=>'post_select', 'post_type'=>'copa' ),
                'mdc_selecoes_relacionadas' => array( 'label'=>'Seleções relacionadas', 'type'=>'post_select_multi', 'post_type'=>'selecao' ),
                'mdc_jogadores_relacionados' => array( 'label'=>'Jogadores relacionados', 'type'=>'post_select_multi', 'post_type'=>'jogador' ),
                'mdc_reportagens_relacionadas' => array( 'label'=>'Reportagens relacionadas', 'type'=>'post_select_multi', 'post_type'=>'post' ),
            ),
            'Mídia' => array(
                'mdc_video_url' => array( 'label'=>'URL do vídeo', 'type'=>'url', 'ajuda'=>'YouTube, Vimeo ou outro endereço compatível com o oEmbed do WordPress.' ),
                'mdc_audio_url' => array( 'label'=>'URL do áudio', 'type'=>'url', 'ajuda'=>'SoundCloud ou outro serviço compatível com o oEmbed do WordPress. Também aceita arquivo de áudio direto.' ),
                'mdc_galeria' => array( 'label'=>'Galeria de imagens', 'type'=>'galeria' ),
                'mdc_videos' => array( 'label'=>'Vídeos relacionados', 'type'=>'videos' ),
            ),
        ),
    );
}

add_action( 'add_meta_boxes', function() {
    foreach ( mdc_field_groups() as $post_type => $groups ) {
        foreach ( $groups as $title => $fields ) {
            add_meta_box(
                'mdc_meta_' . $post_type . '_' . sanitize_key( $title ),
                $title,
                'mdc_render_metabox',
                $post_type,
                'normal',
                'high',
                array( 'fields' => $fields )
            );
        }
    }
} );

function mdc_render_metabox( $post, $box ) {
    $fields = isset( $box['args']['fields'] ) ? $box['args']['fields'] : array();
    wp_nonce_field( 'mdc_save_meta', 'mdc_meta_nonce' );
    echo '<div class="mdc-admin-fields">';
    foreach ( $fields as $key => $field ) {
        $value = get_post_meta( $post->ID, $key, true );
        $type = $field['type'];
        $full_width = in_array( $type, array( 'textarea', 'code', 'videos', 'galeria', 'imagem', 'post_select_multi' ), true );
        echo '<p class="mdc-admin-field' . ( $full_width ? ' mdc-admin-field--full' : '' ) . '"><label style="display:block;font-weight:600;margin-bottom:5px;">' . esc_html( $field['label'] ) . '</label>';

        if ( $type === 'bool' ) {
            echo '<label style="display:flex;align-items:center;gap:8px;font-weight:400;">';
            echo '<input type="checkbox" name="' . esc_attr($key) . '" value="1" ' . checked( $value, '1', false ) . '>';
            echo esc_html__( 'Ativado', 'mundo-da-copa' );
            echo '</label>';
        } elseif ( $type === 'code' ) {
            echo '<textarea name="' . esc_attr($key) . '" rows="7" style="width:100%;font-family:monospace;">' . esc_textarea($value) . '</textarea>';
        } elseif ( $type === 'textarea' ) {
            echo '<textarea name="' . esc_attr($key) . '" rows="4" style="width:100%;">' . esc_textarea($value) . '</textarea>';
        } elseif ( $type === 'post_select' || $type === 'post_select_multi' ) {
            $multiple = $type === 'post_select_multi';
            $vals = $multiple ? get_post_meta( $post->ID, $key, false ) : array( $value );
            if ( $multiple && 1 === count( $vals ) && is_array( $vals[0] ) ) {
                $vals = $vals[0]; // dados antigos gravados como array serializado
            }
            $query_args = array(
                'post_type'      => $field['post_type'],
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
            );

            if ( 'entidade' === $field['post_type'] ) {
                if ( 'mdc_federacao' === $key ) {
                    $query_args['meta_key']   = 'mdc_nivel_entidade';
                    $query_args['meta_value'] = 'nacional';
                } elseif ( 'mdc_confederacao' === $key ) {
                    $query_args['meta_key']   = 'mdc_nivel_entidade';
                    $query_args['meta_value'] = 'continental';
                }
            }

            /*
             * Quando o registro já possui uma categoria masculino/feminino,
             * os relacionamentos relevantes mostram apenas registros da mesma
             * categoria. Isso evita misturar, por exemplo, Brasil masculino
             * e Brasil feminino dentro de uma Copa feminina.
             */
            $categoria_taxonomias = array(
                'copa'    => 'categoria_copa',
                'selecao' => 'categoria_selecao',
                'jogador' => 'categoria_jogador',
            );

            $tipo_atual = get_post_type( $post->ID );
            $tax_atual  = isset( $categoria_taxonomias[ $tipo_atual ] ) ? $categoria_taxonomias[ $tipo_atual ] : '';
            $tax_alvo   = isset( $categoria_taxonomias[ $field['post_type'] ] ) ? $categoria_taxonomias[ $field['post_type'] ] : '';

            if ( $tax_atual && $tax_alvo ) {
                $termos_atuais = wp_get_object_terms( $post->ID, $tax_atual, array( 'fields' => 'slugs' ) );

                if ( ! is_wp_error( $termos_atuais ) && ! empty( $termos_atuais ) ) {
                    $slug_atual = $termos_atuais[0];
                    $slug_alvo  = $slug_atual;

                    if ( 'jogador' === $field['post_type'] && in_array( $slug_atual, array( 'masculina', 'feminina' ), true ) ) {
                        $slug_alvo = 'masculina' === $slug_atual ? 'masculino' : 'feminino';
                    } elseif ( 'jogador' !== $field['post_type'] && 'masculino' === $slug_atual ) {
                        $slug_alvo = 'masculina';
                    } elseif ( 'jogador' !== $field['post_type'] && 'feminino' === $slug_atual ) {
                        $slug_alvo = 'feminina';
                    }

                    $query_args['tax_query'] = array(
                        array(
                            'taxonomy' => $tax_alvo,
                            'field'    => 'slug',
                            'terms'    => $slug_alvo,
                        ),
                    );
                }
            }

            $items = get_posts( $query_args );
            echo '<select name="' . esc_attr($key) . ($multiple ? '[]' : '') . '" style="width:100%;" ' . ($multiple ? 'multiple size="6"' : '') . '>';
            if ( ! $multiple ) echo '<option value="">Selecione...</option>';
            foreach ( $items as $item ) {
                echo '<option value="' . esc_attr($item->ID) . '" ' . selected(in_array($item->ID, array_map('intval',$vals), true), true, false) . '>' . esc_html($item->post_title) . '</option>';
            }
            echo '</select>';
        } elseif ( $type === 'select' ) {
            echo '<select name="' . esc_attr($key) . '" style="width:100%;">';
            echo '<option value="">Selecione...</option>';
            foreach ( (array) $field['options'] as $opt_valor => $opt_rotulo ) {
                echo '<option value="' . esc_attr($opt_valor) . '" ' . selected($value, $opt_valor, false) . '>' . esc_html($opt_rotulo) . '</option>';
            }
            echo '</select>';
        } elseif ( $type === 'imagem' ) {
            $img_id = absint( $value );
            echo '<div class="mdc-campo-imagem" data-mdc-imagem>';
            echo '<div class="mdc-campo-imagem__preview">';
            if ( $img_id ) {
                $preview_size = ( 'copa' === $post->post_type && 'mdc_capa' === $key ) ? 'large' : 'medium';
                $preview_style = ( 'copa' === $post->post_type && 'mdc_capa' === $key )
                    ? 'max-width:320px;max-height:430px;width:auto;height:auto;border-radius:8px;object-fit:contain;'
                    : 'max-width:220px;height:auto;border-radius:6px;';
                echo wp_get_attachment_image( $img_id, $preview_size, false, array( 'style' => $preview_style ) );
            }
            echo '</div>';
            echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($img_id) . '">';
            echo '<p><button type="button" class="button" data-mdc-escolher>Escolher imagem</button> ';
            echo '<button type="button" class="button-link" data-mdc-remover style="color:#b32d2e;">Remover</button></p>';
            echo '</div>';
        } elseif ( $type === 'galeria' ) {
            $ids = array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );
            echo '<div class="mdc-campo-galeria" data-mdc-galeria>';
            echo '<div class="mdc-campo-galeria__itens" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px;">';
            foreach ( $ids as $img_id ) {
                echo wp_get_attachment_image( $img_id, 'thumbnail', false, array( 'style' => 'width:76px;height:76px;object-fit:cover;border-radius:6px;' ) );
            }
            echo '</div>';
            echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr( implode( ',', $ids ) ) . '">';
            echo '<p><button type="button" class="button" data-mdc-escolher>Escolher imagens</button> ';
            echo '<button type="button" class="button-link" data-mdc-remover style="color:#b32d2e;">Limpar</button></p>';
            echo '</div>';
        } elseif ( $type === 'videos' ) {
            echo '<textarea name="' . esc_attr($key) . '" rows="4" style="width:100%;" placeholder="https://www.youtube.com/watch?v=...">' . esc_textarea($value) . '</textarea>';
            echo '<span class="description">Um endereço por linha. YouTube, Vimeo e outros serviços suportados pelo WordPress são incorporados automaticamente.</span>';
        } else {
            echo '<input type="' . esc_attr($type === 'url' ? 'url' : $type) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" style="width:100%;">';
        }
        if ( ! empty( $field['ajuda'] ) ) {
            echo '<span class="description" style="display:block;margin-top:4px;">' . esc_html( $field['ajuda'] ) . '</span>';
        }
        echo '</p>';
    }
    echo '</div>';
}

add_action( 'save_post', function( $post_id ) {
    if ( ! isset($_POST['mdc_meta_nonce']) || ! wp_verify_nonce($_POST['mdc_meta_nonce'], 'mdc_save_meta') ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision($post_id) ) return;
    if ( ! current_user_can('edit_post', $post_id) ) return;

    $type = get_post_type($post_id);
    $groups = mdc_field_groups();
    if ( ! isset($groups[$type]) ) return;

    foreach ( $groups[$type] as $fields ) {
        foreach ( $fields as $key => $field ) {
            if ( $field['type'] === 'post_select_multi' ) {
                // Uma linha de meta por item: permite meta_query exata na busca
                // inversa (quais reportagens citam esta seleção, por exemplo).
                $value = isset($_POST[$key]) ? array_map('absint', (array) wp_unslash($_POST[$key])) : array();
                $value = array_values(array_unique(array_filter($value)));
                delete_post_meta($post_id, $key);
                foreach ( $value as $item ) {
                    add_post_meta($post_id, $key, $item);
                }
            } else {
                $value = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : '';
                if ( $field['type'] === 'bool' ) {
                    $value = isset($_POST[$key]) ? '1' : '0';
                } elseif ( $field['type'] === 'code' ) {
                    $value = current_user_can( 'unfiltered_html' ) ? wp_unslash( $value ) : '';
                } elseif ( $field['type'] === 'number' ) {
                    $value = is_numeric($value) ? $value : '';
                } elseif ( $field['type'] === 'url' ) {
                    $value = esc_url_raw($value);
                } elseif ( $field['type'] === 'textarea' || $field['type'] === 'videos' ) {
                    // Textos longos aceitam a formatação básica do editor.
                    $value = wp_kses_post($value);
                } elseif ( $field['type'] === 'imagem' ) {
                    $value = absint($value);
                } elseif ( $field['type'] === 'galeria' ) {
                    $value = implode( ',', array_filter( array_map( 'absint', explode( ',', (string) $value ) ) ) );
                } else {
                    $value = sanitize_text_field($value);
                }
                update_post_meta($post_id, $key, $value);
            }
        }
    }
} );


/**
 * Seletor de mídia do WordPress nas telas de edição dos registros.
 */
add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }

    $tipo = get_post_type();

    if ( ! $tipo || ! array_key_exists( $tipo, mdc_field_groups() ) ) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script(
        'mdc-admin',
        MDC_THEME_URI . '/assets/js/mdc-admin.js',
        array( 'jquery' ),
        MDC_THEME_VERSION,
        true
    );
} );

add_action( 'admin_head', function() {
	$screen = get_current_screen();

	if ( ! $screen || 'colunista' !== $screen->post_type ) {
		return;
	}
	?>
	<style>
		.post-type-colunista .mdc-admin-fields {
			display:grid;
			grid-template-columns:repeat(2,minmax(0,1fr));
			gap:0 24px;
		}
		.post-type-colunista .mdc-admin-field {
			margin:0 0 18px;
		}
		.post-type-colunista .mdc-admin-field--full {
			grid-column:1/-1;
		}
		.post-type-colunista .mdc-admin-fields input[type="text"],
		.post-type-colunista .mdc-admin-fields input[type="url"],
		.post-type-colunista .mdc-admin-fields input[type="number"],
		.post-type-colunista .mdc-admin-fields select,
		.post-type-colunista .mdc-admin-fields textarea {
			width:100%;
			box-sizing:border-box;
		}
		@media (max-width:782px) {
			.post-type-colunista .mdc-admin-fields {
				grid-template-columns:1fr;
			}
			.post-type-colunista .mdc-admin-field--full {
				grid-column:auto;
			}
		}
	</style>
	<?php
} );


/** Mantém apenas uma categoria editorial por registro. */
add_filter( 'wp_set_object_terms', function( $terms, $object_id, $taxonomy ) {
    if ( ! in_array( $taxonomy, array( 'categoria_copa', 'categoria_selecao', 'categoria_jogador' ), true ) ) {
        return $terms;
    }

    if ( is_array( $terms ) && count( $terms ) > 1 ) {
        return array( reset( $terms ) );
    }

    return $terms;
}, 10, 3 );
