<!-- Fancybox aditional libraries. -->
<script type="text/javascript" src="<?= base_url('lib/plugins/fancybox/jquery.fancybox.pack.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('lib/plugins/fancybox/jquery.easing.pack.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('lib/plugins/fancybox/helpers/jquery.fancybox-media.js?v=1.0.6'); ?>"></script>
<link rel="stylesheet" href="<?= base_url('lib/plugins/fancybox/jquery.fancybox.css'); ?>" type="text/css" media="screen" />
<div class="row">
    <div class="col-sm-12 col-md-12">
        <h1 class="page-header">Galeria de Vídeos</h1>
    </div>
</div>
<div class="row wpn-social-buttons">
    <div class="col-sm-12 col-md-12">
        <?= wpn_widget('addthisbuttons'); ?>
    </div>
</div>
<div class="row">
    <?php
    $num_cols = 1;
    foreach ($videos as $video)
    {
        $col = 12 / $max_cols;
        ?>
        <div class="col-sm-<?= $col; ?> col-md-<?= $col; ?>">
            <div class="thumbnail">
                <div class="inner-video">
                    <?php
                    $image_properties = array(
                        'src' => 'http://img.youtube.com/vi/' . $video->link . '/0.jpg',
                        'class' => 'img-responsive'
                    );
                    // Link antigo para exibição do vídeo em outra página.
                    // echo anchor('video/'.$video->link.'/'.wpn_fakelink($video->titulo), img($image_properties));
                    ?>
                    <a href="https://www.youtube.com/watch?v=<?= $video->link; ?>" class="fancybox-media" ><?= img($image_properties); ?></a>
                </div>
            </div>
            <div class="caption">
                <h4><?= $video->titulo; ?></h4>
            </div>
        </div>
        <?php
        // Cria uma nova linha de acordo com a quantidade de ítens por linha.
        if ($num_cols == $max_cols)
        {
            echo '</div><div class="row">';
            $num_cols = 1;
        } else
            $num_cols = $num_cols ++;
    }
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.fancybox-media').fancybox({
            openEffect: 'none',
            closeEffect: 'none',
            helpers: {
                media: {}
            }
        });
    });
</script>