<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Cut Image</title>
</head>

<script src="http://code.jquery.com/jquery-1.8.3.js"></script>

<script src="/assets/theme/default/js/cut_image.js"></script>
<script>
    var item_calcs = <?= json_encode($calcs) ?>;
    var item = <?= json_encode($item) ?>;
    $(function () {
        var sheetdim = [item_calcs.sheet_width, item_calcs.sheet_height];
        draw_lines(sheetdim, item_calcs.cuts, item);
    });
</script>

<style>
.cutback { width: 260px; font: normal 12px Helvetica, sans-serif; padding: 2px 0 0 0; text-align: center; }

#cut_input { position: absolute; border: 1px solid grey; top: 75px; left: 300px; padding: 5px;}

</style>

<body>
<? /*
<div id="cut_input">
  <form>
  sheet : <input class="cutback" id="cutback1" value="[20.752730392268,27.317952367572,7.2343506474226e-7]"><br>
  points:<textarea class="cutback" id="cutback2" >
 [[[0,0],[20.752730392268,0]],[[20.513,0],[20.75394716327,27.317952367572]],[[0,27.319143574133],[20.752730392268,27.179998088198]],[[0,0],[0.17766144862044,27.317952367572]]]
     </textarea><br>
  </form>
  <center><button id="draw_line">Draw Line</button></center>
</div>
*/ ?>
<svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg"
   version="1.1" width="6in" height="7in" id="svg1">

  <g id="layer1">
    <text x="50" y="63" id="title" xml:space="preserve"
       style="font-size:24px;fill:#000000;fill-opacity:1;stroke:none;">
       <tspan x="50" y="63" id="title_text"> Title Text </tspan> 
    </text>

    <rect width="100" height="50" x="55" y="85" 
       id="orientation"
       style="fill:none;stroke:#000000;stroke-opacity:1" />

    <rect width="320" height="320" x="110" y="250"
       id="bigbox"
       style="fill:none;stroke:#000000;stroke-width:1;stroke-dasharray:12,1;" />
    <text id="sheet_dim" xml:space="preserve"
       style="font-size:32px;fill:#000000;fill-opacity:1;">
       <tspan x="160" y="420" id="text_sheet_dim"> 00.00w x 00.00h </tspan>
    </text>

    <path d="M 110,250 L 140,570" id="left_line"
       style="fill:none;stroke:black;stroke-width:1;stroke-opacity:1;stroke-dasharray:2,1;" />
    <text id="cutback_left" xml:space="preserve"
       style="font-size:18px;fill:#000000;fill-opacity:1;">
       <tspan x="85" y="240" id="ltop_val">&lt;ltop&gt;</tspan>
       <tspan x="85" y="590" id="lbtm_val">&lt;lbtm&gt;</tspan>
    </text>

    <path d="M 430,250 L 410,570" id="right_line"
       style="fill:none;stroke:black;stroke-width:1;stroke-opacity:1;stroke-dasharray:2,1;" />
    <text id="cutback_right" xml:space="preserve"
       style="font-size:18px;fill:#000000;fill-opacity:1;">
       <tspan x="405" y="240" id="rtop_val">&lt;rtop&gt;</tspan>
       <tspan x="405" y="590" id="rbtm_val">&lt;rbtm&gt;</tspan>
    </text>

    <path d="M 110,250 L 430,270" id="top_line"
       style="fill:none;stroke:black;stroke-width:1;stroke-opacity:1;stroke-dasharray:2,1;" />
    <text id="cutback_top" xml:space="preserve"
       style="font-size:18px;fill:#000000;fill-opacity:1;">
       <tspan x="60" y="255" id="left_val">&lt;left&gt;</tspan>
       <tspan x="440" y="255" id="right_val">&lt;rgt&gt;</tspan>
    </text>

    <rect width="100" height="26" x="225" y="210" id="top_box"
       style="fill:none;stroke:grey;stroke-width:1;stroke-opacity:1;" />
    <text text-anchor="middle" xml:space="preserve" style="font-size:22px;fill:#000000;fill-opacity:1;">
       <tspan x="280" y="230" id="top_length">000.00 t</tspan> </text>

    <rect width="100" height="26" x="225" y="580" id="btm_box"
       style="fill:none;stroke:grey;stroke-width:1;stroke-opacity:1;" />
    <text text-anchor="middle" xml:space="preserve" style="font-size:22px;fill:#000000;fill-opacity:1;">
       <tspan x="280" y="600" id="btm_length">000.00 b</tspan> </text>

    <rect width="100" height="26" x="5" y="390" id="left_box"
       style="fill:none;stroke:grey;stroke-width:1;stroke-opacity:1;" />
    <text text-anchor="middle" xml:space="preserve" style="font-size:22px;fill:#000000;fill-opacity:1;">
       <tspan x="60" y="410" id="lef_length">000.00 l</tspan> </text>

    <rect width="100" height="26" x="445" y="390" id="rig_box"
       style="fill:none;stroke:grey;stroke-width:1;stroke-opacity:1;" />
    <text text-anchor="middle" xml:space="preserve" style="font-size:22px;fill:#000000;fill-opacity:1;">
       <tspan x="500" y="410" id="rig_length">000.00 r</tspan> </text>

  </g>
</svg>

</body>
</html>
