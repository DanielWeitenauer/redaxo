<div id="<?=$this->modal_id;?>" class="modal fade" role="dialog" aria-labelledby="<?=$this->modal_title_id;?>" style="text-align:left;">
    <div class="modal-dialog" role="document">
        <div class="modal-content form-vertical">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h3 id="<?=$this->modal_title_id;?>" class="modal-title"><?=$this->modal_title;?></h3>
            </div>
            <div class="modal-body">
                <?=$this->modal_body;?>
            </div>
            <div class="modal-footer">
                <?=$this->modal_button;?>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=rex_i18n::msg('form_abort');?></button>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $("#<?=$this->modal_id;?>").modal();
        });
    </script>
</div>

