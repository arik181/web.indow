<script src="/assets/theme/default/js/fulfillment_reports.js"></script>
<style>
    .report label {
        width: 100%;
    }
    .report {
        margin-bottom: 40px;
    }
    #report1 {
        margin-top: -40px;
    }
</style>
<iframe id="csv_frame" style="display: none;"></iframe>
<form class="report" method="post" id="report1">
    <input type="hidden" name="type" value="manufacturing">
    <h2>Manufacturing Report</h2>
    <div class="row">
        <div class="col-md-2">
            <label>
                Order Status
                <? $extra_codes = array('all' => 'All', '100-650' => '100-650', '350-500' => '350-500', '300-600' => '300-600') ?>
                <?= form_dropdown('status', $extra_codes + $codes, '300', 'class="form-control input-sm"') ?>
            </label>
        </div>
        <div class="col-md-2">
            <label>
                Dealer
                <?= form_dropdown('dealer', $groups, '', 'class="form-control input-sm"') ?>
            </label>
        </div>
        <div class="col-md-2">
        </div>
        <div class="col-md-2">
        </div>
        <div class="col-md-2">
            <br>
            <button class="print_report btn btn-blue">Print Report</button>
        </div>
        <div class="col-md-2">
            <br>
            <button class="csv_report btn btn-blue">Download CSV</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <label>
                Filter Dates
                <?php $date_filters = Array('build_date' => "Build Date", 'created' => "Order Date", 'commit_date' => "Commit Date"); ?>
                <?= form_dropdown('date_filter', $date_filters, '', 'class="form-control input-sm"') ?>
            </label>
        </div>
        <div class="col-md-2">
            <label>
                Date From
                <input name="date_from" class="form-control input-sm datepicker">
            </label>
        </div>
        <div class="col-md-2">
            <label>
                Date To
                <input name="date_to" class="form-control input-sm datepicker">
            </label>
        </div>
    </div>
</form>

<form class="report" method="post" id="report2">
    <input type="hidden" name="type" value="orders">
    <h2>Orders Report</h2>
    <div class="row">
        <div class="col-md-2">
            <label>
                Order Status
                <? $extra_codes = array('all' => 'All') ?>
                <?= form_dropdown('status', $extra_codes + $codes, '300', 'class="form-control input-sm"') ?>
            </label>
        </div>
        <div class="col-md-2">
            <label>
                Dealer
                <?= form_dropdown('dealer', $groups, '', 'class="form-control input-sm"') ?>
            </label>
        </div>
        <div class="col-md-2">
        </div>
        <div class="col-md-2">
        </div>
        <div class="col-md-2">
            <br>
            <button class="print_report btn btn-blue">Print Report</button>
        </div>
        <div class="col-md-2">
            <br>
            <button class="csv_report btn btn-blue">Download CSV</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <label>
                Filter Dates
                <?php $date_filters = Array('created' => "Order Date", 'commit_date' => "Commit Date"); ?>
                <?= form_dropdown('date_filter', $date_filters, '', 'class="form-control input-sm"') ?>
            </label>
        </div>
        <div class="col-md-2">
            <label>
                Date From
                <input name="date_from" class="form-control input-sm datepicker">
            </label>
        </div>
        <div class="col-md-2">
            <label>
                Date To
                <input name="date_to" class="form-control input-sm datepicker">
            </label>
        </div>
    </div>
</form>

<form class="report" method="post" id="report3">
    <input type="hidden" name="type" value="estimates">
    <h2>Estimates Report</h2>
    <div class="row">
        <div class="col-md-4">
            <label>
                Dealer
                <?= form_dropdown('dealer', $groups, '', 'class="form-control input-sm"') ?>
            </label>
        </div>
        <div class="col-md-2">
            <label>
                Date From
                <input name="date_from" class="form-control input-sm datepicker">
            </label>
        </div>
        <div class="col-md-2">
            <label>
                Date To
                <input name="date_to" class="form-control input-sm datepicker">
            </label>
        </div>
        <div class="col-md-2">
            <br>
            <button class="print_report btn btn-blue">Print Report</button>
        </div>
        <div class="col-md-2">
            <br>
            <button class="csv_report btn btn-blue">Download CSV</button>
        </div>
    </div>
</form>

<form class="report" method="post" id="report4">
    <input type="hidden" name="type" value="panels">
    <h2>Panels Report</h2>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-2">
            <label>
                Date From
                <input name="date_from" class="form-control input-sm datepicker">
            </label>
        </div>
        <div class="col-md-2">
            <label>
                Date To
                <input name="date_to" class="form-control input-sm datepicker">
            </label>
        </div>
        <div class="col-md-2">
            <br>
            <button class="print_report btn btn-blue">Print Report</button>
        </div>
        <div class="col-md-2">
            <br>
            <button class="csv_report btn btn-blue">Download CSV</button>
        </div>
    </div>
</form>

<form class="report" method="post" id="report5">
    <input type="hidden" name="type" value="users">
    <h2>Users Report</h2>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-2">

        </div>
        <div class="col-md-2">

        </div>
        <div class="col-md-2">
            <br>
            <button class="print_report btn btn-blue">Print Report</button>
        </div>
        <div class="col-md-2">
            <br>
            <button class="csv_report btn btn-blue">Download CSV</button>
        </div>
    </div>
</form>

<form class="report" method="post" id="report3">
    <input type="hidden" name="type" value="balance">
    <h2>Outstanding Balance Report</h2>
    <div class="row">
        <div class="col-md-4">
            <label>
                Dealer
                <?= form_dropdown('dealer', $groups, '', 'class="form-control input-sm"') ?>
            </label>
        </div>
        <div class="col-md-2">
            <label>
                Date From
                <input name="date_from" class="form-control input-sm datepicker">
            </label>
        </div>
        <div class="col-md-2">
            <label>
                Date To
                <input name="date_to" class="form-control input-sm datepicker">
            </label>
        </div>
        <div class="col-md-2">
            <br>
            <button class="print_report btn btn-blue">Print Report</button>
        </div>
        <div class="col-md-2">
            <br>
            <button class="csv_report btn btn-blue">Download CSV</button>
        </div>
    </div>
</form>

<form class="report" method="post" id="report4">
    <input type="hidden" name="type" value="jobsites">
    <h2>Jobsites Report</h2>
    <div class="row">
        <div class="col-md-4">
            <label>
                Ordered/Unordered
                <select class="form-control" name="itemtype">
                    <option value="both">Both</option>
                    <option value="ordered">Ordered</option>
                    <option value="unordered">Unordered</option>
                </select>
            </label>
        </div>
        <div class="col-md-2">

        </div>
        <div class="col-md-2">

        </div>
        <div class="col-md-2">
            <br>
            <button class="print_report btn btn-blue">Print Report</button>
        </div>
        <div class="col-md-2">
            <br>
            <button class="csv_report btn btn-blue">Download CSV</button>
        </div>
    </div>
</form>
