<script>

$(document).ready(function(){
  $('.whatif').keyup(function(){
    var panels = 0
    var queued = 0
    var currentdays = 0
    var minsperpanel = 0
    var numpeople = 0
    var hourspershift = 0
    var workdaysperweek = 0
    var shiftsperday = 0
    var productivitypercent = 0
    var minsperhour = 0
    if (!isNaN($('#panels').val()))
      panels = $('#panels').val();
    if (!isNaN($('#queued').val()))
      queued = $('#queued').val();
    if (!isNaN($('#currentdays').val()))
      currentdays = $('#currentdays').val();
    if (!isNaN($('#minsperpanel').val()))
      minsperpanel = $('#minsperpanel').val();
    if (!isNaN($('#numpeople').val()))
      numpeople = $('#numpeople').val();
    if (!isNaN($('#hourspershift').val()))
      hourspershift = $('#hourspershift').val();
    if (!isNaN($('#workdaysperweek').val()))
      workdaysperweek = $('#workdaysperweek').val();
    if (!isNaN($('#shiftsperday').val()))
      shiftsperday = $('#shiftsperday').val();
    if (!isNaN($('#productivitypercent').val()))
      productivitypercent = $('#productivitypercent').val();
    minsperhour = 60;
    panelspermin = 1/minsperpanel;
    panelsperday = hourspershift * shiftsperday * numpeople * (productivitypercent/100.0) * minsperhour * panelspermin;
    
    if($(this).attr('id') == "currentdays")
    {
      var possiblepanels = currentdays * panelsperday - queued;
      if (!isNaN(possiblepanels) && isFinite(possiblepanels) && possiblepanels > 0)
      {
        $('#panels').val(Math.floor(possiblepanels));
      }
    }
    else
    {
      var days = (parseFloat(panels) + parseFloat(queued)) / panelsperday;
      
      if (!isNaN(days) && isFinite(days))
      {
        $('#currentdays').val(parseFloat(days).toFixed(2));
      }
    }
  });
  $('.estimates').keyup(function(){
    var percentclosed = $('#percentclosed').val();
    var estimatetimeframe = $('#estimatetimeframe').val();
    var estimatesperday = $('#estimatesperday').val();
    var panelsperestimate = $('#panelsperestimate').val();
    var result = $('#panelsintimeframe');
    var total = estimatetimeframe * estimatesperday *panelsperestimate * (percentclosed / 100.0);
    if (!isNaN(total) && isFinite(total))
    {
     if (!isNaN(estimatetimeframe) && isFinite(estimatetimeframe)) 
        $("#estimateddays").html(estimatetimeframe);
      result.html(total);
    }
  });
});
</script>

  <div class="box-sec">
        <div class="top-head">
          <h1>WHAT IF?</h1>
          <h2>Estimate Activity</h2>
        </div>
        <div class="block-sec">
          <div class="left-sec">
            <div class="pqd">
              <div class="nop">
                <label>Number of Panels</label>
                <input tabindex="1" class="whatif" type="text" id="panels" />
              </div>
              <div class="nop">
                <label>Panels in Queues</label>
                <input tabindex="2" class="whatif" type="text" value="<?= $queued; ?>" id="queued" />
              </div>
              <div class="nop">
                <label>Current total days</label>
                <input tabindex="3" class="whatif" type="text" id="currentdays" />
              </div>
            </div>
            <div class="assumption-acc">
            <a  data-toggle="collapse" href="javascript:void(0);" data-target="#demo" style="width:100%;"><h1>  Assumption <i class="fa fa-chevron-down" style="margin-top: 7px;float: right;margin-right: 10px;" ></i></h1> </a>               <div class="assumption-list collapse in" id="demo">
                <ul>
                  <li>
                    <label>Minutes per panel*</label>
                    <input class="whatif" type="text" id="minsperpanel" />
                  </li>
                  <li>
                    <label># of People</label>
                    <input class="whatif" type="text" id="numpeople" />
                  </li>
                  <li>
                    <label>Hours per Shift</label>
                    <input class="whatif" type="text" id="hourspershift" />
                  </li>
                  <li>
                    <label># of Work days per Week</label>
                    <input class="whatif" type="text" id="workdaysperweek" />
                  </li>
                  <li>
                    <label>Shifts per Day</label>
                    <input class="whatif" type="text" id="shiftsperday" />
                  </li>
                  <li>
                    <label>% Productivity</label>
                    <input class="whatif" type="text" id="productivitypercent" />
                  </li>
                </ul>
                <p class="note"><sup>∗</sup>Based on average for all products</p>
              </div>
            </div>
          </div>
          <div class="right-sec">
            <div class="right-ess">
              <div class="ess-closed">
                <em>days</em>
                <input tabindex="5" class="estimates" id="estimatetimeframe" type="text" />
                <label>% closed in</label>
                <input tabindex="4" class="estimates" id="percentclosed" type="text" class="ess-in">
              </div>
              <div class="ess-closed"> <!-- nuked per devin <em class="cn">75</em>-->
                <label>Estimates per Day</label>
                <input tabindex="6" class="estimates" id="estimatesperday" type="text" />
              </div>
              <div class="ess-closed"> <!-- nuked per devin <em class="cn">7</em>-->
                <label>Avg. Panels per Estimate</label>
                <input tabindex="7" class="estimates" id="panelsperestimate" type="text" />
              </div>
              <div class="ess-closed result"> <!-- nuked per devin <em class="cn">89</em> <big>|</big>--><span class="result-final" id="panelsintimeframe">0</span>
                <label>Result</label>
              </div>
              <div class="ess-closed"> <!--TODO: align right-->
                Panel(s) over <span id="estimateddays">0</span> day(s)
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="box-sec funnel">
        <div class="top-head grey">
          <h1>Funnel Data</h1>
        </div>
        <div class="funnel-sec">
          <div class="funnel-box">
            <div class="funnel-box-inner">
              <h1>Estimated Products</h1>
              <h2><?= $estimate_funnel->active_estimates;?></h2>
              <h5>Average # of Panels <span><?= round($estimate_funnel->avg_panels,2);?></span></h5>
              <h5>Average Sqft/panel <span><?= round($estimate_funnel->avg_sqft,2);?></span></h5>
            </div>
          </div>
          <div class="funnel-box">
            <div class="funnel-box-inner">
              <h1>Measured Openings</h1>
              <h2><?= $opening_funnel->openings;?></h2>
              <h5>Avg per Location <span><?= round($opening_funnel->panels_per_location,2);?></span></h5>
              <h5>Average Sqft/panel <span><?= round($opening_funnel->avg_sqft,2);?></span></h5>
            </div>
          </div>
          <div class="funnel-box">
            <div class="funnel-box-inner">
              <h1>Total Panels</h1>
              <h2><?= $product_funnel;?></h2>
              <h5>&nbsp; <span></span></h5>
              <h5>&nbsp;<span></span></h5>
            </div>
          </div>
        </div>
      </div>
      <div class="box-sec pipline">
        <div class="top-head grey">
          <h1>Pipeline</h1>
        </div>
        <div class="pipline-sec">
          <div class="pipline-srh">
            <form method="post" action="">
              <label>Select Region</label>
              <select name="group_id">
                <option>Select</option>
                <?
                foreach ($groups as $group)
                {

                  ?>
                  <option <?= (@$form['group_id']==$group->id?'selected':'') ?> value="<?= $group->id;?>"><?= $group->group_name;?></option>
                  <?
                }
                ?>
              </select>
            </div>
            <div class="pipline-time">
              <label>Date</label>
              <input type="text" name="start_date" class="cal_date" placeholder="Start Date" value="<?= @$form['start_date'];?>" />
              <label>To</label>
              <input type="text" name="end_date" class="cal_date" placeholder="End Date" value="<?= @$form['end_date'];?>" />
            </div>
            <input type="submit" class="srh-btn" value="Search">
          </form>
        </div>
        <div class="pipline-result">
          <ul>
            <li> Results</li>
          </ul>
        </div>
        <div class="pipline-result-list">
          <ul>
            <li>
              <h1>Total Panels</h1>
              <a href="#"><strong><?= $pipeline_counts['productCount'];?></strong></a></li>
            <li>
              <h1>Total Orders</h1>
              <a href="#"><strong><?= $pipeline_counts['orderCount'];?></strong></a></li>
          </ul>
          <table id="productsTable" class="display table table-hover" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Actions</th>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Company</th>
                    <th>Commit Date</th>
                </tr>
            </thead>
          </table>
        </div>
      </div>

<?php 

initializeDataTable($selector       = "#productsTable", 
                    $ajaxEndPoint   = "/fulfillment/filtered_orders_json/" . $form['group_id'] . "/" . urlencode($form['start_date']) . "/" . urlencode($form['end_date']),
                    $columns        = array("id", 
                                            "order_name", 
                                            "customer", 
                                            "status", 
                                            "dealer", 
                                            "commit_date",
                                            ),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/orders/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no records available."
                    );
?>

<script>
$(document).ready(function(){
   $('.cal_date').datepicker(); 
});
</script>
















