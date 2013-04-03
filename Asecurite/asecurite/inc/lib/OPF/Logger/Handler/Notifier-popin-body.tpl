<div class="OPFLog_item">
    <table width="100%">
        <tr>
            <th><?php echo $this->itemNumber; ?></th>
            <td>
            <!-- START  -->

                <!-- ITEM HEADER -->
                <div class="OPFLog_itemHeader">
                    <div class="OPFLog_title" style="color:<?php echo $this->itemTitleColor; ?>">
                        <?php echo $this->itemTitle; ?>
                    </div>
                    <?php if (!empty($this->itemTag)) : ?>
                        <div class="OPFLog_tag">
                            <?php echo $this->itemTag; ?>
                        </div>
                    <?php endif; ?>
                    <div style="clear:both"></div>
                </div>

                <!-- ITEM BODY -->
                <div class="OPFLog_itemBody">

                    <!-- ITEM BODY MESSAGE -->
                     <?php if (!empty($this->itemMessage)) : ?>
                        <div class="OPFLog_message">
                            <?php echo $this->itemMessage; ?>
                        </div>
                    <?php endif; ?>

                    <!-- ITEM BODY DATAS -->
                    <?php if(empty($this->itemDataTitle)) : ?>
                        <?php echo $this->itemData; ?>
                    <?php else : ?>
                        <a style="display:block;" href="javascript:OPF.logger.toggleDisplayBT('OPFLog_msg<?php echo $this->itemNumber; ?>');">

                            <div class="OPFLog_data" style="white-space:nowrap">
                                <div style="float:left;padding:5px 0;" class="viewDatas">
                                    <div style="float:left;font-size:20pt;">&rarr;</div>
                                    Click for view <?php echo $this->itemDataTitle; ?>
                                </div>
                                <pre id="OPFLog_msg<?php echo $this->itemNumber; ?>" style="display:none;">
                                    <?php echo $this->itemData; ?>
                                </pre>
                            </div>
                            <div style="clear:both"></div>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- ITEM FOOTER -->
                <div class="OPFLog_itemFooter">
                    <table>
                        <tr>
                            <!-- ITEM FOOTER LAST BACKTRACE -->
                            <td class="OPFLog_lastbacktrace OPFLog_lastbacktraceLink">
                                <a style="display:block;" href="javascript:OPF.logger.toggleDisplayBT('OPFLog_backtracesPHP<?php echo $this->itemNumber; ?>');">
                                    BackTrace :
                                    <?php if(isset($this->itemBacktraces[1])) : ?>
                                        <span><?php echo $this->itemBacktraces[1][0]; ?></span>
                                        <br />
                                    <?php endif; ?>
                                    Line [ <?php echo $this->itemBacktraces[0][2]; ?> ] on File : [ <?php echo $this->itemBacktraces[0][1]; ?> ]
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <!-- ITEM FOOTER BACKTRACES -->
                            <td id="OPFLog_backtracesPHP<?php echo $this->itemNumber; ?>" class="OPFLog_backtracesList">
                                <table>
                                    <?php $txt = '&nbsp;';
                                       foreach ($this->itemBacktraces as $backtrace) : ?>
                                        <tr>
                                        <th><?php echo $txt; ?></th>
                                        <td class="OPFLog_function"><?php echo $backtrace[0]; ?></td>
                                        <td class="OPFLog_file"><?php echo $backtrace[1]; ?></td>
                                        <td class="OPFLog_line"><?php echo $backtrace[2]; ?></td>
                                        </tr>
                                    <?php $txt = $this->itemBacktracesCount--; endforeach; ?>

                                </table>
                            </td>
                        </tr>
                    </table>
                </div>

             <!-- END  -->
            </td>
        </tr>
    </table>
</div>