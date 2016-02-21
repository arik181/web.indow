<?php
set_time_limit(0);
$mybase = array(
	"tangerine" => "1.0 , 2.3"
);

//$sauce = (split(",", $mybase["tangerine"]));
//print "\n";
//$new = $sauce[0] + $sauce[1];
//var_dump($new);

$data=array();

//$data=array(14.97,14.97,34.72,34.72,37.81,37.81);
//$data[]=array(30,30,40,40,50,50.2);

$data[]=array(29.125,29.156,57.156,57.094,64.094,64.063);
$data[]=array(29.094,29.125,57.188,57.250,64.344,64.094);
$data[]=array(34.938,35.156,79.719,79.469,86.781,86.969);
$data[]=array(34.969,35.125,79.500,79.531,86.781,87.219);
$data[]=array(34.906,34.969,79.000,78.813,86.375,86.250);
$data[]=array(34.906,35.063,78.719,78.625,86.094,85.969);
$data[]=array(39.000,38.938,79.281,79.281,88.594,88.000);
$data[]=array(39.063,39.219,79.188,79.156,88.375,88.094);
$data[]=array(39.156,39.156,79.094,78.938,88.188,88.281);
$data[]=array(39.125,39.031,78.031,78.406,87.281,87.719);
$data[]=array(61.406,60.188,55.813,55.719,82.906,83.219);
$data[]=array(61.531,61.531,55.563,55.563,82.969,82.656);
$data[]=array(23.000,22.875,54.344,54.344,58.844,59.094);
$data[]=array(23.031,23.063,54.344,54.406,59.031,59.281);
$data[]=array(23.063,22.906,54.375,54.375,58.844,59.219);
$data[]=array(23.000,23.000,54.375,54.500,58.813,59.281);
$data[]=array(22.938,22.875,54.125,54.125,58.719,58.688);
$data[]=array(23.031,23.031,54.125,54.125,58.844,58.781);
$data[]=array(23.063,23.000,54.188,54.250,58.938,58.813);
$data[]=array(22.906,22.875,54.219,54.219,58.781,58.813);


/* Self-check data */
/* A perfect cube of sides 10 units */
$check_data=array(10,10,10,10,14.14213562373095,14.14213562373095);
/* 3,4,5 triangle */
$check_data=array(30,30,40,40,50,50);


$precision = 10; // precision of calculation
bcscale($precision); // set precision

function get_calcs($window, $item) {
    $fp = fuzzyPointGen($window, $item);
    $myMin = $fp[0];
    $points = $fp[1];

    $newpoints = shiftpoints($points, $item);
    $sheetdim=fitsheet($newpoints);
    $cuts = makecutlist($sheetdim, $newpoints);
    $remainingcuts = cutRemover($sheetdim, $cuts);

    $error_margin = @abs(bcsub(distance($points[1],$points[3]),$points[5]));
    return array(
        'sheet_dimensions' => $sheetdim,
        'cuts' => $remainingcuts,
        'error_margin' => $error_margin
    );
}

function main($data) {
    return;
	foreach ($data as $dimlist) {
		$fp = fuzzyPointGen($dimlist);
		$myMin = $fp[0];
		$points = $fp[1];

		//$index = array_keys($allerror, min($allerror));
		//$points = $allpoints[$index[0]];

		$newpoints = shiftpoints($points);
		$sheetdim=fitsheet($newpoints);
		$cuts = makecutlist($sheetdim, $newpoints);
		$remainingcuts = cutRemover($sheetdim, $cuts);
		//print_r($myMin);
		//var_dump($points);
		//print "\n-Sheet Dimensions-\n";
		print "[$sheetdim[0], $sheetdim[1]]\n";
		//print "$myMin\n";
		//print "\n-Remaining Cuts-\n";
		//print_r($remainingcuts);

		print "The Error in F: " . abs(bcsub(distance($points[1],$points[3]),$dimlist[5])) . "\n";
	}
}
function getangles($data) {
	$a = $data[2];
	$b = $data[0];
	$c = $data[4];
	$d = $data[3];
	$e = $data[1];
	$f = $data[5];
	$angles = array();
        $angles[] = (rad2deg(acos((pow($a,2) + pow($b,2) - pow($c,2)) / (2 * $a * $b)))); # angle AB
        $angles[] = (rad2deg(acos((pow($b,2) + pow($d,2) - pow($f,2)) / (2 * $b * $d)))); # angle BD
        $angles[] = (rad2deg(acos((pow($e,2) + pow($d,2) - pow($c,2)) / (2 * $e * $d)))); # angle ED
        $angles[] = (rad2deg(acos((pow($e,2) + pow($a,2) - pow($f,2)) / (2 * $e * $a)))); # angle AE
        $angles[] = (rad2deg(acos((pow($e,2) + pow($c,2) - pow($d,2)) / (2 * $e * $c)))); # angle EC
        $angles[] = $angles[4] + (rad2deg(acos((pow($a,2) + pow($c,2) - pow($b,2)) / (2 * $a * $c)))); # angle AC
	return $angles;
}

function getpoints($data, $anglelist) {
	$a = $data[2];
	$b = $data[0];
	$c = $data[4];
	$d = $data[3];
	$e = $data[1];
	$f = $data[5];
        $radians = array();
        foreach ($anglelist as $value) {
		$radians[] = (deg2rad($value));
	};
	$points = array();
        $points[] = array(0,0);
        $points[] = array($e,0);
        $points[] = array($c*cos($radians[4]),$c*sin($radians[4]));
        $points[] = array($a*cos($radians[5]),$a*sin($radians[5]));
	return $points;
}

function getpoints2($data) {
    // Points are generated directly from measurement, avoiding the cosine() rounding error
	$a = $data[2]; //left - r0
	$b = $data[0]; //top - r1
	$c = $data[4]; //diagR - D
	$d = $data[3]; //right
	$e = $data[1]; //bottom
	$f = $data[5]; //diagL
	
	// Use Area and Hero's formula locate right corner coordinates
	$s = ($c + $d + $e)/2; 
	$area = @bcsqrt(bcmul(bcmul($s,($s-$c)),bcmul(($s-$d),($s-$e)))); // Hero's formula
	$y1 = bcdiv(bcmul(2,$area),$e); // c
	$x1 = bcsqrt(bcsub(bcpow($c,2), bcpow($y1,2))); // d
	
	// Use http://www.ambrsoft.com/TrigoCalc/Circles2/Circle2.htm for top left coord.
	$delta = @bcmul(1/2,bcsqrt(bcmul(bcmul(($c+$a+$b),($c+$a-$b)),bcmul(($c-$a+$b),(-$c+$a+$b)))));
	$a2mb2 = bcsub(bcpow($a,2),bcpow($b,2)); // a^2 minus b^2
	$c2 = bcpow($c,2); // c^2
	$y0 = bcadd(bcadd(bcdiv($y1,2) , bcdiv(bcmul($y1,$a2mb2), bcmul(2,$c2))) , bcdiv(bcmul($delta,$x1),($c2)));
	$x0 = bcsub(bcadd(bcdiv($x1,2) , bcdiv(bcmul($x1,$a2mb2), bcmul(2,$c2))) , bcdiv(bcmul($delta,$y1),($c2)));
	
	$points = array(
        array(0,0),
        array($e,0),
        array($x1,$y1), 
        array($x0,$y0) 
	);
	
	return $points;
}


function distance($point1, $point2) {
	$dx = bcsub($point2[0], $point1[0]);
	$dy = bcsub($point2[1], $point1[1]);
        $dist = bcadd(bcpow($dx,2), bcpow($dy,2));
	return bcsqrt($dist);
}

function errorf($data, $pointslist) {
	$f = $data[5];
	$p1 = $pointslist[1];
	$p2 = $pointslist[3];
	$dist =  distance($p1,$p2); // distance, with precision
        $error = bcsub($dist,$f);
        $error = str_replace("-","",$error); // remove negative sign '-'

        return $error;
}

function fuzzyPointGen($data, $item) {
        $a = $data[2];
        $b = $data[0];
        $c = $data[4];
        $d = $data[3];
        $e = $data[1];
        $f = $data[5];

        $allpoints = array();
        $allerror = array();
        $alltemp = array();

        $myMin = 10000; // large starting value
        $myPoint = "";
        $int = 9;
        $start = 0.0625;
		$step = 0.0125;
        foreach (range(0,$int) as $i) {
            $atemp = $a - $start + ($step*$i);
            foreach (range(0,$int) as $j) {
                $btemp = $b - $start + ($step*$j);
                foreach (range(0,$int) as $k) {
                    $ctemp = $c - $start + ($step*$k);
                    foreach (range(0,$int) as $l) {
                        $dtemp = $d - $start + ($step*$l);
                        foreach (range(0,$int) as $m) {
                            $etemp = $e - $start + ($step*$m);
                            $tempdata = array($btemp,$etemp,$atemp,$dtemp,$ctemp,$f);
                            //$tempangles = getangles($tempdata);
                            //$temppoints = getpoints($tempdata,$tempangles);
                            $temppoints = getpoints2($tempdata);
							$newMin = errorf($tempdata,$temppoints);
                            if ($myMin > $newMin) { // smaller?
                                $myMin = $newMin; // set new myMine
                                $myPoint = $temppoints; // record the points
								$newpoints = shiftpoints($temppoints, $item);
								$sheetdim=fitsheet($newpoints);
								//print "Error: $newMin [x,y] = [".$sheetdim[0].", ".$sheetdim[1]."]\n";
								//print "	".$btemp."-".$etemp."-".$atemp."-".$dtemp."-".$ctemp."-".$f."\n";
                            }
                        }
                    }
                }
            }
        }
        //print $myMin;
        //print_r( $myPoint );
        return array($myMin, $myPoint);
}

function shiftpoints($pointslist, $item) {
	$newpoints = array();
	$xpoints = array();
        $ypoints = array();

        foreach ($pointslist as $pt) {
	    $xpoints[] = $pt[0];
	    $ypoints[] = $pt[1];
	}

	$xmin = min($xpoints);
	$ymin = min($ypoints);

        foreach ($xpoints as $key=>$x) {
	    $xpoints[$key] = $xpoints[$key] + abs($xmin);
	    $ypoints[$key] = $ypoints[$key] + abs($ymin);
	    $newpoints[] = array($xpoints[$key],$ypoints[$key]);
        }

    if ($item->frame_depth_id == 4 && $item->product->product_id == 1) {
        $offset = 1.5;
    } else {
        if ($item->product->cut_image_offset === null) {
            $offset = 1.375;
        } else {
            $offset = (float) $item->product->cut_image_offset;
        }
    }

	$newpoints[1][0] = $newpoints[1][0] - $offset;
	$newpoints[2][0] = $newpoints[2][0] - $offset;
	$newpoints[2][1] = $newpoints[2][1] - $offset;
	$newpoints[3][1] = $newpoints[3][1] - $offset;

	return $newpoints;
}

function fitsheet($pointslist) {
	$rectangledim = array();
	$xpoints = array();
        $ypoints = array();

	foreach($pointslist as $pt) {
            $xpoints[] = $pt[0];
            $ypoints[] = $pt[1];
	}
	$rectangledim[] = max($xpoints);
	$rectangledim[] = max($ypoints);

	return $rectangledim;

}

function makecutlist($sheetdim, $points) {
	$cutlist = array();
	foreach ($points as $key=>$pt) {
	    $temp1 = $pt;
	    if ($key == 3) {
	        $temp2 = $points[0];
	    } else {
	        $temp2 = $points[$key+1];
	    }
	    $m = @($temp2[1]-$temp1[1])/($temp2[0]-$temp1[0]);
	    $b = $temp1[1]-$m*$temp1[0];

	    if ($key % 2 == 0) {
	        $cutlist[] = array(
	                   array(0,$b),
	                   array($sheetdim[0],$m*$sheetdim[0]+ $b)
	                 );
	    } else {
	        $cutlist[] = array(
	                  @ array(-$b/$m,0),
	                  @ array(($sheetdim[1]-$b)/$m,$sheetdim[1])
	                 );
	    }
	}
	return $cutlist;
}

function cutRemover($sheetsize, $cutlist) {
	$keptcuts = array();
	$removedcuts = array();
	foreach ($cutlist as $cut) {
	    $p1 = $cut[0];
	    $p2 = $cut[1];
	    $p1check = ((abs($p1[0])<=.03125) or (abs($p1[0]-$sheetsize[0])<=.03125)) and ((abs($p1[1])<=.03125) or (abs($p1[1]-$sheetsize[1])<=.03125));
	    $p2check = ((abs($p2[0])<=.03125) or (abs($p2[0]-$sheetsize[0])<=.03125)) and ((abs($p2[1])<=.03125) or (abs($p2[1]-$sheetsize[1])<=.03125));
	    if (($p1check) or ($p2check)) {
	        $keptcuts[] = $cut;
	    } else {
	        $keptcuts[] = array(
	            array(0,0),
	            array(0,0)
	        );
	    }
	}
	return $keptcuts;
}
//$anglelist = getangles($data);
//$points = getpoints($data, $anglelist);

//print_r($data);
//print_r($anglelist);
//main($data);

