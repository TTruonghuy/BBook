<?php
        $base = $document->getBase();
        if (!empty($base)) {
            echo '<base href="' . $base . '" />';
            $document->setBase('');
			$app = JFactory::getApplication();
    $tplparams	= $app->getTemplate(true)->params;
	$hic = htmlspecialchars($tplparams->get('hic'));
	$fc = htmlspecialchars($tplparams->get('fc'));
    $tc = htmlspecialchars($tplparams->get('tc'));
    $gc = htmlspecialchars($tplparams->get('gc'));
    $pc = htmlspecialchars($tplparams->get('pc'));
	$cal = htmlspecialchars($tplparams->get('cal'));
	$cser = htmlspecialchars($tplparams->get('cser'));
	$hcl = htmlspecialchars($tplparams->get('hcl'));
	$slide1 = htmlspecialchars($tplparams->get('slide1'));
	$slide2 = htmlspecialchars($tplparams->get('slide2'));
	$slide3 = htmlspecialchars($tplparams->get('slide3'));
	$slide4 = htmlspecialchars($tplparams->get('slide4'));
	$slide5 = htmlspecialchars($tplparams->get('slide5'));
	$slic = htmlspecialchars($tplparams->get('slic'));
	$ps1 = htmlspecialchars($tplparams->get('ps1'));
	$ps2 = htmlspecialchars($tplparams->get('ps2'));
	$ps3 = htmlspecialchars($tplparams->get('ps3'));
	$ps4 = htmlspecialchars($tplparams->get('ps4'));
	$ps5 = htmlspecialchars($tplparams->get('ps5'));
	$ps6 = htmlspecialchars($tplparams->get('ps6'));
	$ps7 = htmlspecialchars($tplparams->get('ps7'));
	$ps8 = htmlspecialchars($tplparams->get('ps8'));
	$ps9 = htmlspecialchars($tplparams->get('ps9'));
	$ps10 = htmlspecialchars($tplparams->get('ps10'));
	$ps11 = htmlspecialchars($tplparams->get('ps11'));
	$ps12 = htmlspecialchars($tplparams->get('ps12'));
	$pbc = htmlspecialchars($tplparams->get('pbc'));
	$iic = htmlspecialchars($tplparams->get('iic'));
	$vic = htmlspecialchars($tplparams->get('vic'));
	$aab = htmlspecialchars($tplparams->get('aab'));
	$aic = htmlspecialchars($tplparams->get('aic'));
    $anb = htmlspecialchars($tplparams->get('anb'));
	$pab = htmlspecialchars($tplparams->get('pab'));
	$ib2ac = htmlspecialchars($tplparams->get('ib2ac'));
	$qx = htmlspecialchars($tplparams->get('qx'));
	$cbx = htmlspecialchars($tplparams->get('cbx'));
	$cbx1 = htmlspecialchars($tplparams->get('cbx1'));
		$cix = htmlspecialchars($tplparams->get('cix'));
		$dsb = htmlspecialchars($tplparams->get('dsb'));
		$gic = htmlspecialchars($tplparams->get('gic'));
		$gix = htmlspecialchars($tplparams->get('gix'));
		$fsb = htmlspecialchars($tplparams->get('fsb'));
		$dc = htmlspecialchars($tplparams->get('dc'));
        }
    ?>
         <link href="<?php echo JURI::base()?>/<?php echo $document->params->get('fav'); ?>" rel="icon" type="image/x-icon" />

 