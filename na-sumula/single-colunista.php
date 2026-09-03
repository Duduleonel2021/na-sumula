<?php
/** Perfil individual de Colunista — Mundo da Copa. */
get_header();
while ( have_posts() ) : the_post();
  $id=get_the_ID(); $d=mdc_dados_colunista($id); $posts=mdc_posts_do_colunista($id,12);
?>
<article class="mdc-colunista-profile">
  <header class="mdc-colunista-profile__hero"><div class="mdc-container mdc-colunista-profile__inner">
    <div class="mdc-colunista-profile__photo"><?php if(has_post_thumbnail()) { the_post_thumbnail('large',array('loading'=>'eager','fetchpriority'=>'high','alt'=>esc_attr($d['nome']))); } else { ?><span aria-hidden="true"><?php echo esc_html(mb_strtoupper(mb_substr($d['nome'],0,1))); ?></span><?php } ?></div>
    <div class="mdc-colunista-profile__copy"><div class="mdc-breadcrumbs"><?php if(function_exists('mdc_breadcrumb')) mdc_breadcrumb(); ?></div><span class="mdc-section-kicker">COLUNA</span><?php if($d['coluna']) : ?><h1><?php echo esc_html($d['coluna']); ?></h1><h2 class="mdc-colunista-profile__author"><?php echo esc_html($d['nome']); ?></h2><?php else : ?><h1><?php echo esc_html($d['nome']); ?></h1><?php endif; ?><?php if($d['cargo']) : ?><p class="mdc-colunista-profile__role"><?php echo esc_html($d['cargo']); ?></p><?php endif; ?><?php if($d['bio']) : ?><p><?php echo esc_html($d['bio']); ?></p><?php endif; ?></div>
  </div></header>
  <section class="mdc-section"><div class="mdc-container mdc-colunista-profile__content">
    <div><div class="mdc-section-heading"><div><span class="mdc-section-kicker">PUBLICAÇÕES</span><h2><?php echo $d['coluna'] ? 'Artigos da coluna ' . esc_html($d['coluna']) : 'Artigos de ' . esc_html($d['nome']); ?></h2></div></div><?php if($posts): ?><div class="mdc-post-grid mdc-post-grid--related"><?php foreach($posts as $post_item): get_template_part('template-parts/card-post',null,array('id'=>$post_item->ID)); endforeach; ?></div><?php else: ?><div class="mdc-empty-state"><strong>Nenhum artigo publicado ainda.</strong><span>Os textos deste colunista aparecerão aqui.</span></div><?php endif; ?></div>
    <aside class="mdc-colunista-profile__aside"><span class="mdc-section-kicker">SOBRE</span><h2>Perfil editorial</h2><?php if($d['cargo']): ?><p><strong>Especialidade</strong><br><?php echo esc_html($d['cargo']); ?></p><?php endif; ?><?php if($d['site']): ?><p><a href="<?php echo esc_url($d['site']); ?>" target="_blank" rel="noopener noreferrer">Site pessoal →</a></p><?php endif; ?><?php foreach(array('instagram'=>'Instagram','x'=>'X','facebook'=>'Facebook','linkedin'=>'LinkedIn') as $key=>$label): if(!empty($d[$key])): ?><p><a href="<?php echo esc_url($d[$key]); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($label); ?> →</a></p><?php endif; endforeach; ?></aside>
  </div></section>
</article>
<?php endwhile; get_footer(); ?>
