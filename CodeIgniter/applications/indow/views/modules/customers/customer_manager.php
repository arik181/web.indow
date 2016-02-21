<div id="customer_manager">
<?
            $add_customer_tab_data      = array( array('name' => 'New Customer',
                                                       'id'   => 'customer_tab1'),
                                                 array('name' => 'Existing Customer',
                                                       'id'   => 'customer_tab2')
                                                   );
            $tabbar_css_id              = "customer";
            if (!isset($tab)) {
                $tab           = 0;
            }
            echo generate_tabbar( $tabbar_css_id, $add_customer_tab_data, $tab );
?>
<script>
    <? if (isset($start_customers)) { ?>
        var customer_manager_customers = <?= json_encode($start_customers) ?>;
    <? } else { ?>
        var customer_manager_customers = [];
    <? } ?>
</script>
<style>
    /* Only you, can help prevent inline CSS. */
    #customer_manager_table .icon {
        margin-right: 5px;    
    }
    #customer_search_results {
        top: 10px;
        position: absolute;
        width: 200px;
        background-color: #ffffff;
        border: 1px solid #999999;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        display: none;
        z-index: 4;
    }
    .search_customer {
        padding: 4px;
        border-bottom: 1px solid #cccccc;
    }
    .search_customer:last-child {
        border-bottom: 0px;
    }
    .search_customer.active {
        background-color: #cecece;
    }
    #srcont {
        position: relative;
        top: 22px;
    }
    #ajax_customer_search {
        width: 50%;
    }
</style>
<script src="/assets/theme/default/js/customer_manager.js"></script>
<div class="tab-content view-tab">
    <div id="customer_tab1" class="tab-pane <?= $tab == 0 ? 'active' : '' ?> content-row tab1">
        <?= $this->load->view('modules/customers/cmaddform'); ?>
    </div>
    <div id="customer_tab2" class="tab-pane <?= $tab == 1 ? 'active' : '' ?> content-row">
        <span id="srcont">
            <table class="display table dataTable no-footer" id="customer_search_results"></table>
        </span>
        <input class="input-sm form-control" id="ajax_customer_search" placeholder="Search by Customer Email/Name to Add" />
        <table id="customer_manager_table" class="display table table-hover" cellspacing="0">
            <thead>
                <tr>
                    <th>Actions</th>
                    <th>Name</th>
                    <th>Details</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
</div>