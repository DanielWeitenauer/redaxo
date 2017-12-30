<table class="table table-striped table-hover">
    <thead>
        <?=$this->table_head;?>
    </thead>

    <?php if (isset($this->table_body) && $this->table_body != ''):?>
        <tbody>
            <?=$this->table_body;?>
        </tbody>
    <?php endif;?>
</table>
