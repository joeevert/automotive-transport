<?php
/*
Template Name: Instant Quote
Template Post Type: page
*/

get_header();

$post = get_post(get_the_ID());

?>

  <main>
    <section>
      <div class="text-center">
        <h2 class="">Instant Quote</h2>
      </div>
      <div id="iqProgressBar">
        <div class="iq-progress-bar-container">
          <div class="progress-step">
            <div id="shippingInfoStep" class="progress-step__inner isActive">
              Zip Code
            </div>
          </div>
          <div class="progress-step">
            <div id="vehicleDetailsStep" class="progress-step__inner">
              Vehicle
            </div>
          </div>
          <div class="progress-step">
            <div id="outputStep"  class="progress-step__inner">
              Shipping
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="instant-quote-container">
      <div id="shippingInfo">
        <div class="iq-row">
          <div class="input-group-container">
            <label for="origin">Origin Zip Code</label>
            <input id="origin" type="number" name="origin" min="0" />
          </div>
          <div class="input-group-container">
            <label for="destination">Destination Zip Code</label>
            <input id="destination" type="number" name="destination" min="0" />
          </div>
          <div class="input-group-container">
            <label for="desiredDate">Desired Shipping Date</label>
            <input id="desiredDate" name="desiredDate" type="date" min="<?php echo date('Y-m-d')?>"/>
          </div>
        </div>
        <div class="iq-next-container">
          <button onclick="instantQuoteModule.calculateDistance()" class="capabl-btn__round">Next</button>
        </div>
      </div>

        <div id="vehicleDetails" style="visibility: hidden;">
            <div class="iq-row">
                <div class="input-group-container">
                    <label for="make">Make</label>
                    <input id="make" type="text" name="make" placeholder="make"/>
                </div>
                <div class="input-group-container">
                    <label for="model">Model</label>
                    <input id="model" type="text" name="model" placeholder="model"/>
                </div>
                <div class="input-group-container">
                    <label for="year">Year</label>
                    <input id="year" type="text" name="year" placeholder="year"/>
                </div>
            </div>
            <div class="iq-row">
                <div class="input-group-container">
                    <label for="vehicleType">Vehicle Type</label>
                    <select id="vehicleType" name="vehicleType">
                        <option value="" disabled selected>Select vehicle type</option>
                        <option value="car">Car</option>
                        <option value="truck">Truck</option>
                        <option value="van">Van</option>
                        <option value="suv">SUV</option>
                    </select>
                </div>
                <div class="input-group-container">
                    <label for="operationalStatus">Operational Status</label>
                    <select id="operationalStatus" name="operationalStatus">
                        <option value="" disabled selected>Select operational status</option>
                        <option value="running">Operational</option>
                        <option value="not-running">Non-Operational</option>
                    </select>
                </div>
                <div class="iq-row iq-helper-text">
                    <P><span class="font-weight-bold">Can't find your vehicle?</span> Our instant quote tool is not
                        yet configured for certain vehicles including those that would be considered oversized or
                        non-operational<br>
                        Rest assured we can still help! <a href="https://offsettransport.com/contact-us/"
                                                           target="_self">Simply request a custom quote here</a>
                    </P>
                    <div class="iq-next-container">
                        <button onclick="instantQuoteModule.captureVehicleDetails()" class="capabl-btn__round">Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

      <div id="output" style="visibility: hidden;">
        <div class="output-row">
          <p>SHIPPING SPEED - <a href="https://offsettransport.com/faqs/#how-fast-can-you-ship-my-car" target="_blank">LEARN MORE</a></p>
          <h3>Shipping Options</h3>
          <ul class="shipping-speed-options">
              <li>
                  <input type="radio" id="openStandard" class="speedOption" name="shippingSpeed" value="" data-speed="Standard" data-type="Open">
                  <label for="openStandard" id="openStandardLabel">
                    <div>Open Trailer</div>
                    Standard -
                  </label>
              </li>
              <li>
                  <input type="radio" id="openFast" class="speedOption" name="shippingSpeed" value="" data-speed="Faster" data-type="Open">
                  <label for="openFast" id="openFastLabel">
                    <div>Open Trailer</div>
                    Faster -
                  </label>
              </li>
              <li>
                  <input type="radio" id="openNitro" class="speedOption" name="shippingSpeed" value="" data-speed="Fastest" data-type="Open">
                  <label for="openNitro" id="openNitroLabel">
                    <div>Open Trailer</div>
                    Fastest -
                  </label>
              </li>
            </ul>
          <ul class="shipping-speed-options">
              <li>
                  <input type="radio" id="closedStandard" class="speedOption" name="shippingSpeed" value="" data-speed="Standard" data-type="Enclosed">
                  <label for="closedStandard" id="closedStandardLabel">
                    <div>Enclosed Trailer</div>
                    Standard -
                  </label>
              </li>
              <li>
                  <input type="radio" id="closedFast" class="speedOption" name="shippingSpeed" value="" data-speed="Faster" data-type="Enclosed">
                  <label for="closedFast" id="closedFastLabel">
                    <div>Enclosed Trailer</div>
                    Faster -
                  </label>
              </li>
              <li>
                  <input type="radio" id="closedNitro" class="speedOption" name="shippingSpeed" value="" data-speed="Fastest" data-type="Enclosed">
                  <label for="closedNitro" id="closedNitroLabel">
                    <div>Enclosed Trailer</div>
                    Fastest -
                  </label>
              </li>
            </ul>
          <button type="submit" onclick="instantQuoteModule.submitQuote()" class="capabl-btn__round">Continue to Deposit</button>
        </div>
      </div>

    </section>

        <?php if ( have_posts() ) :

            while ( have_posts() ) :

                the_post();

                the_content();

            endwhile;

            the_posts_navigation();

        else :

            get_template_part( 'templates/content', 'none' );

        endif;
        ?>

    </main>

<?php get_footer(); ?>