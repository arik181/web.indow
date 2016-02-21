<script>
    $(function () {
        $('#show-grade-desc').click(function () {
            $('#grade-descriptions').slideDown();
            $(this).fadeOut();
        });
        $('#hide-grade-desc').click(function () {
            $('#grade-descriptions').slideUp();
            $('#show-grade-desc').fadeIn();
        });
    });
</script>

<div class="key_cont" id="key_cont">
    <div class="pad15">
        <h2><?= empty($key_title) ? 'Estimate Key' : $key_title ?></h2>
        <div class="row">
            <div class="col-xs-8">
                <div class="row">
                    <div class="col-xs-4"><span class="product_key">STD -</span> Standard</div>
                    <div class="col-xs-4"><span class="product_key">CG -</span> Commercial Grade</div>
                    <div class="col-xs-4"><span class="product_key">SG -</span> Shade Grade</div>
                </div>
                <div class="row">
                    <div class="col-xs-4"><span class="product_key">A-STD -</span> Acoustic Grade</div>
                    <div class="col-xs-4"><span class="product_key">PG -</span> Privacy Grade</div>
                    <div class="col-xs-4"><span class="product_key">A-BG -</span> Acoustic + Blackout</div>
                </div>
                <div class="row">
                    <div class="col-xs-4"><span class="product_key">MG -</span> Museum Grade</div>
                    <!--div class="col-xs-4"><span class="product_key">BG -</span> Blackout Grade</div-->
                    <div class="col-xs-4"><span class="product_key">A-CG -</span> Acoustic + Commercial</div>
                    <div class="col-xs-4"><span class="product_key">SP -</span> Sleep Panel</div>
                </div>
            </div>
        </div><br>
        <? if (empty($hide_validation)) { ?>
        <div class="row">
            <div class="col-xs-11">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="key-box"></div> Incorrect number format.  Please use decimals only.<br>
                        <div class="key-box filled"></div> Size exceeds max allowable size for selected product type.
                    </div>
                    <div class="col-xs-6">
                        <span title="Special Geometry" class="geo-key">&#x22BE;</span> Check this box if you have a nonstandard shaped window. Checking this box will add Indow's special geometry fee as a fee line item in the totals block below. Use largest W x H dimensions for special shaped windows. 
                    </div>
                </div>
            </div>
        </div>
        <? } ?>
        <div class="pull-right showhide-grade-desc" id="show-grade-desc">READ MORE <i class="fa fa-chevron-right"></i></div>
    </div>
    <div id="grade-descriptions" class="pad15">
        <h3 style="font-weight: bold">Grade Descriptions</h3><br>
        <p><span class="grade-title">Standard Grade:</span> Provides an excellent balance of comfort, energy efficiency and noise dampening. The most popular choice.</p>

        <p><span class="grade-title">Acoustic Grade (1/4"):</span> Using thicker acrylic glazing than Standard Grade, Acoustic Grade reduces the noise coming through single pane windows by 18.9 dBa, according to testing done by Portland State
        University's Green Building Research Lab. An 18.9 dBa reduction "feels,” subjectively, like a 70% reduction in noise. Can be combined with Blackout or Commercial Grade.</p>

        <p><span class="grade-title">Museum Grade:</span> Protect your furniture, carpets, and artwork with Museum Grade inserts that filter 98% of UV radiation from sunlight coming in through your windows. There is absolutely no visual difference between
        the Standard and Museum Grade acrylic.</p>

        <p><span class="grade-title">Commercial Grade:</span> Although the acrylic glazing used in Standard Indow inserts is among the hardest of plastics, it can still scratch. Commercial Grade Indow inserts have an extra abrasionresistant
        coating to provide additional protection against scratching. Choose Commercial Grade for inserts that will be frequently moved or cleaned. There is absolutely no visual difference between the Standard and 
        Commercial Grade acrylic. Can be combined with Standard or Acoustic Grade.</p>

        <p><span class="grade-title">Privacy Grade:</span> Choose Privacy Grade inserts to increase the privacy of your home from prying eyes or nosy neighbors. Privacy Grade is a white, translucent acrylic that allows for diffused light transmission but
        blocks out any details or defined shadows. Perfect for bathrooms.</p>

        <!--p><span class="grade-title">Blackout Grade:</span> These black panels block all light coming through your windows and provide the same noise reduction and thermal insulation of Standard Grade Indow inserts. Ideal for light sleepers, nightshift
        workers, and paleo lifestyle followers. Available in Standard or Acoustic Grade thicknesses.</p-->

        <p><span class="grade-title">Shade Grade:</span> Reduce the heat and UV rays while maintaining a high level of visible light and clarity. Shade Grade will reduce Solar Heat Gain through the window opening with an SHGC of 0.52 while maintaining a
        visible light transmittance of 63%. Shade Grade has a soft green hue.</p>

		<p><span class="grade-title">Sleep Panel:</span> These white panels block all light coming through your windows and provide the same  thermal insulation of Standard Grade Indow inserts, with enhanced noise reduction.  The furniture grade finish
		is highly cleanable, durable and paintable.  Ideal for light sleepers, nightshift workers, and paleo lifestyle followers. </p>
        <div class="pull-right showhide-grade-desc" id="hide-grade-desc">HIDE <i class="fa fa-chevron-up"></i></div>
    </div>
</div>
