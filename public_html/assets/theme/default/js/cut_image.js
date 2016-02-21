    function draw_lines(sheetdim, remainingcuts, item) {
        //console.log(sheetdim, remainingcuts, item);
        if (item) {
            jQuery("#title_text").text("Window number: " + item.unit_num);
        }
 //      var sheetdim = JSON.parse(jQuery("#cutback1").val());
 //      var remainingcuts = JSON.parse(jQuery("#cutback2").val());
       var incr = 10; // increment per points

       if (sheetdim[0] < sheetdim[1]) { // swap width+height to change orientation
            jQuery("#orientation").attr("height","100").attr("width","50");
       }

       /* obtain the cut-back values, parseFloat() to ensure it a FLOAT */
       function zn(num) {
            return isNaN(num) ? 0 : num;
       }
       var rtop = to2decimal(parseFloat(sheetdim[0]) - parseFloat(remainingcuts[1][1][0]));
       var rbtm = to2decimal(parseFloat(sheetdim[0]) - parseFloat(remainingcuts[1][0][0]));
       var left = to2decimal(parseFloat(sheetdim[1]) - parseFloat(remainingcuts[2][0][1]));
       var rig = to2decimal(parseFloat(sheetdim[1]) - parseFloat(remainingcuts[2][1][1]));
       var ltop = to2decimal(parseFloat(remainingcuts[3][1][0]));
       var lbtm = to2decimal(parseFloat(remainingcuts[3][0][0]));

       /* Obtain the cut points */
       var rtop_pt = cut_pt(rtop);
       var rbtm_pt = cut_pt(rbtm);
       var left_pt = cut_pt(left);
       var right_pt = cut_pt(rig);
       var ltop_pt = cut_pt(ltop);
       var lbtm_pt = cut_pt(lbtm);

       /* Check right line */
       if ((rtop_pt == rbtm_pt) && (rtop_pt != 0)) { // ignore if 0, else adjust values 
//console.log("check right: " + rtop, rbtm);
           if (rtop > rbtm) { // rtop is larger
               rtop_pt += 1; // incr rtop
           } else if (rtop < rbtm) { // rbtm it larger
               rbtm_pt += 1; // incr rtop
           }
       }
       /* Check left line */
       if ((ltop_pt == lbtm_pt) && (ltop_pt != 0)) { // ignore if 0, else adjust values
//console.log("check left: " + ltop, lbtm);
           if (ltop > lbtm) { // ltop is larger
               ltop_pt += 1; // incr ltop
           } else if (ltop < lbtm) { // lbtm it larger
               lbtm_pt += 1; // incr ltop
           }
       }
       /* Check top line */
       if ((left_pt == right_pt) && (left_pt != 0)) { // ignore if 0, else adjust valu
//console.log("check top: " + left, rig);
           if (left > rig) { // left is larger
               left_pt += 1; // incr left
           } else if (left < rig) { // right it larger
               right_pt += 1; // incr right
           }
       }

//console.log("rtop rbtm left rig ltop lbtm => " + rtop,rbtm,left,rig,ltop,lbtm);
//console.log("cut pt: " + rtop_pt,rbtm_pt +"/"+ left_pt,right_pt +"/"+ ltop_pt,lbtm_pt);

        /* show the cutback value if not 0 */
        (rtop_pt == 0) ? jQuery('#rtop_val').hide() : jQuery('#rtop_val').show(); jQuery('#rtop_val').text("<" + rtop + ">");
        (rbtm_pt == 0) ? jQuery('#rbtm_val').hide() : jQuery('#rbtm_val').show(); jQuery('#rbtm_val').text("<" + rbtm + ">");
        (left_pt == 0) ? jQuery('#left_val').hide() : jQuery('#left_val').show(); jQuery('#left_val').text("<" + left + ">");
        (right_pt == 0) ? jQuery('#right_val').hide() : jQuery('#right_val').show(); jQuery('#right_val').text("<" + rig + ">");
        (ltop_pt == 0) ? jQuery('#ltop_val').hide() : jQuery('#ltop_val').show(); jQuery('#ltop_val').text("<" + ltop + ">");
        (lbtm_pt == 0) ? jQuery('#lbtm_val').hide() : jQuery('#lbtm_val').show(); jQuery('#lbtm_val').text("<" + lbtm + ">");

        /* draw the 3 lines */
        var left_line = "M " + (110 + (incr*ltop_pt)) + ",250 L " + (110 + (incr*lbtm_pt)) + ",570";
        var right_line = "M " + (430 - (incr*rtop_pt)) + ",250 L " + (430 - (incr*rbtm_pt)) + ",570";
        var top_line = "M 110," + (250 + (incr*left_pt)) + " L 430," + (250 + (incr*right_pt)) ;
        jQuery('#left_line').attr('d', left_line);
        jQuery('#right_line').attr('d', right_line);
        jQuery('#top_line').attr('d', top_line);

        /* adjust the <cut-back> position */
        (rtop_pt != 0) ? jQuery('#rtop_val').attr('x', (405 - incr*rtop_pt)) : false ;
        (rbtm_pt != 0) ? jQuery('#rbtm_val').attr('x', (405 - incr*rbtm_pt)) : false ;
        (left_pt != 0) ? jQuery('#left_val').attr('y', (255 + incr*left_pt)) : false ;
        (right_pt != 0) ? jQuery('#right_val').attr('y', (255 + incr*right_pt)) : false ;
        (ltop_pt != 0) ? jQuery('#ltop_val').attr('x', (85 + incr*ltop_pt)) : false ;
        (lbtm_pt != 0) ? jQuery('#lbtm_val').attr('x', (85 + incr*lbtm_pt)) : false ;

        /* calculate the cut-back lengths */
        //console.log(remainingcuts);
//  var rig_length = dist(remainingcuts[1]);
// console.log(ltop, rtop, lbtm, rbtm);
        var rig_length      = parseFloat(sheetdim[1] - rig).toFixed(2);
        var rig_length_plus = parseFloat(sheetdim[1] - rig + 0.03).toFixed(2);
        var top_length      = parseFloat(sheetdim[0] - zn(ltop) - zn(rtop)).toFixed(2);
        var top_length_plus = parseFloat(sheetdim[0] - zn(ltop) - zn(rtop) + 0.03).toFixed(2);
// var top_length = dist(remainingcuts[2]);
        var lef_length      = parseFloat(sheetdim[1] - left).toFixed(2);
        var lef_length_plus = parseFloat(sheetdim[1] - left + 0.03).toFixed(2);
//  var lef_length = dist(remainingcuts[3]);
//  var btm_length = (sheetdim[0] - lbtm - rbtm).toFixed(2);
        var btm_length      = parseFloat(sheetdim[0] - zn(lbtm) - zn(rbtm)).toFixed(2);
        var btm_length_plus = parseFloat(sheetdim[0] - zn(lbtm) - zn(rbtm) + 0.03).toFixed(2);

        jQuery('#rig_length').text(rig_length + "r");
        jQuery('#top_length').text(top_length + "t");
        jQuery('#lef_length').text(lef_length + "l");
        jQuery('#btm_length').text(btm_length + "b");  

		/*
        jQuery('#trav_b_len').text(btm_length_plus);
        jQuery('#trav_t_len').text(top_length_plus);
        jQuery('#trav_l_len').text(lef_length_plus);
        jQuery('#trav_r_len').text(rig_length_plus);

        jQuery('#tube_b_len').text(btm_length_plus);
        jQuery('#tube_t_len').text(top_length_plus);
        jQuery('#tube_l_len').text(lef_length_plus);
        jQuery('#tube_r_len').text(rig_length_plus);
*/
        /* Draw the sheet dimensions inside the box */
        var sheet_text = to2decimal(sheetdim[0]) + "w x " + to2decimal(sheetdim[1]) + "h";
        jQuery("#text_sheet_dim").text(sheet_text);
		return {b: btm_length, t: top_length, l: lef_length, r: rig_length};
    } /* end of draw_lines routine */

       function to2decimal(n) { // return only 2 decimal, rest are trimmed. Rounding at 6 decimal
          var number = parseFloat(n);
          var N = (parseInt(number.toPrecision(6)*100)/100).toFixed(2);
          return N;
       }

       function cut_pt(n){ /* return the cut_pt */
          var pt = 0; // initialise point
          if (n >= 1.9) {
             pt = 4.5;
          } else if (n >= 1.5) {
             pt = 3.75;
          } else if (n >= 1.0) {
             pt = 3;
          } else if (n >= 0.5) {
             pt = 2.25;
          } else if (n >= 0.03) {
             pt = 1.5;
          }
          return pt;
       }

       function dist(points) {
          var x = parseFloat(points[0][0]) - parseFloat(points[1][0]);
          var y = parseFloat(points[0][1]) - parseFloat(points[1][1]);
          var dst = Math.sqrt((x*x) + (y*y)).toFixed(2);
          return dst;
       }
