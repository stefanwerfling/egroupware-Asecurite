<style type="text/css">
    <?php include('OPF/Logger/Handler/Notifier-styles.css'); ?>
</style>
<script type="text/javascript">
    <?php include('OPF/Logger/Handler/Notifier-scripts.js'); ?>
</script>
<div id="OPFLog">
    <div class="OPFLog_Header">
        OPF Notifier
        <span>
            - for toggle display this popin press CTRL + SPACE'
        </span>
        <div class="OPFLog_closeButton" title="Close the Popin">
            <a href="javascript:OPF.logger.displayToggle();">&otimes;</a>
        </div>
    </div>
    <div class="OPFLog_Body">
      <?php echo $this->content; ?>
    </div>
</div>
