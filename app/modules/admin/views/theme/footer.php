
            </div>
            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                <?php echo wpn_lang('wpn_pagerendered', 'Page rendered in'); ?> <strong>{elapsed_time}</strong> <?php echo wpn_lang('wpn_seconds', 'Seconds'); ?>. Versão <?php echo WPN_VERSION; ?>
                </div>
                <p>&copy; <?php echo date('Y') ?> <a href="http://wpanel.org" target="_blank">Wpanel CMS</a>. Todos os direitos reservados.</p>
            </footer>
        </div>
        <script>
        var roxyFileman = '<?php echo base_url("fileman/index.html"); ?>';
        CKEDITOR.replace( 'editor', {
            filebrowserBrowseUrl:roxyFileman,
            filebrowserImageBrowseUrl:roxyFileman+'?type=image',
            removeDialogTabs: 'link:upload;image:upload'
        });
        </script>
    </body>
</html>
