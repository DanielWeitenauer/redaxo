<div class="modal fade" id="<?=$this->modal_id;?>" style="text-align:left;">
    <div class="modal-dialog">
        <form class="modal-content form-vertical" action="<?=$this->modal_url;?>" method="post" enctype="multipart/form-data" data-pjax-container="#rex-page-main">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"><?=$this->modal_title;?></h3>
            </div>
            <div class="modal-body">
                <?=$this->modal_body;?>
            </div>
            <div class="modal-footer">
                <?=$this->modal_button;?>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=rex_i18n::msg('form_abort');?></button>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function() {
            $("#<?=$this->modal_id;?>").modal();
        });
    </script>
</div>

