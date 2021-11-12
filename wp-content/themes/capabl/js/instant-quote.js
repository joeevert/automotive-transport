const instantQuoteModule = (function () {
    console.log("instant quote module loaded");

    // URL for Google Maps script
    const googleMapsUrl =
        "https://maps.googleapis.com/maps/api/js?key=AIzaSyBtqW3aMBlm1UIL9fnvrsV9Qlqtq0jC1CM&libraries=&v=weekly";

    const errorBorder = "3px solid red";

    // Value to multipy by to meter to get miles
    const metersToMilesValue = 0.000621371;

    const order = {
        id: null,
        year: null,
        make: null,
        model: null,
        vehicleType: null,
        operationalStatus: null,
        desiredShippingDate: null,
        originZip: null,
        destinationZip: null,
        trailerSelection: "open",
        speedSelection: "standard",
        cost: 0,
    };

    // Thresholds for distances
    const distances = {
        short: 500,
        medium: 1500,
        long: 1501,
    };

    // Map of rates based on trailer type, distance, and speed
    const rates = {
        open: {
            short: {
                standard: 0.6,
                fast: 0.7,
                nitro: 0.8,
            },
            medium: {
                standard: 0.55,
                fast: 0.65,
                nitro: 0.75,
            },
            long: {
                standard: 0.5,
                fast: 0.6,
                nitro: 0.7,
            },
        },
        closed: {
            short: {
                standard: 1.2,
                fast: 1.3,
                nitro: 1.4,
            },
            medium: {
                standard: 1.1,
                fast: 1.2,
                nitro: 1.3,
            },
            long: {
                standard: 1.0,
                fast: 1.1,
                nitro: 1.2,
            },
        },
    };

    // Placeholder object for shipping costs
    const shippingCosts = {
        open: {
            standard: 200,
            fast: 225,
            nitro: 250,
        },
        closed: {
            standard: 300,
            fast: 325,
            nitro: 350,
        },
    };



    /**
     * Main function to get the distance between the origin & destion then
     * calculate the shipping options
     */
    function calculateDistance() {
        if (validateZipCodes()) {
            getDesiredDate();
            const originZip = document.getElementById("origin").value;
            const destinationZip = document.getElementById("destination").value;
            order.originZip = originZip;
            order.destinationZip = destinationZip;
            const service = new google.maps.DistanceMatrixService();
            service.getDistanceMatrix(
                {
                    origins: [originZip],
                    destinations: [destinationZip],
                    travelMode: google.maps.TravelMode.DRIVING,
                    unitSystem: google.maps.UnitSystem.IMPERIAL,
                    avoidHighways: false,
                    avoidTolls: false,
                },
                (response, status) => {
                    if (status !== "OK") {
                        alert("Error was: " + status);
                    } else {
                        const results = response.rows[0].elements;
                        handleMatrixResult(results[0]);
                    }
                }
            );

            document.getElementById("shippingInfo").style.visibility = "hidden";
            document.getElementById("vehicleDetails").style.visibility = "visible";

            // Progress Bar
            document.getElementById("shippingInfoStep").classList.remove('isActive');
            document.getElementById("vehicleDetailsStep").classList.add('isActive');

        }
    }

    /**
     * Function to handle matrix results with improved error handling
     *
     * @param results
     */
    function handleMatrixResult(results) {
        if (results.distance !== undefined) {
            let distanceMiles = Math.ceil(
                results.distance.value * metersToMilesValue
            );
            getShippingCost(distanceMiles);
        }
        populateRadioOptions();
    }

    /**
     * Function to get the shipping costs for each of the trailer/speed options
     *
     * @param {*} mileage
     */
    function getShippingCost(mileage) {
        const distanceKey = setDistanceRange(mileage);
        let openRates = rates.open[distanceKey];
        let closedRates = rates.closed[distanceKey];
        for (const [key, value] of Object.entries(openRates)) {
            let minOpenCost = shippingCosts.open[key];
            let calculatedOpenCost = Math.ceil(value * mileage);
            if (calculatedOpenCost >= minOpenCost) {
                shippingCosts.open[key] = calculatedOpenCost;
            }
        }
        for (const [key, value] of Object.entries(closedRates)) {
            let minEnclosedCost = shippingCosts.closed[key];
            let calculatedEnclosedCost = Math.ceil(value * mileage);
            if (calculatedEnclosedCost >= minEnclosedCost) {
                shippingCosts.closed[key] = calculatedEnclosedCost;
            }
        }
    }

    /**
     * Helper funciton to set the rate
     * @param {*} mileage
     */
    function setDistanceRange(mileage) {
        let range = "short";
        if (mileage > distances.short && mileage < distances.long) {
            range = "medium";
        } else if (mileage >= distances.log) {
            range = "long";
        }

        return range;
    }

    /**
     * Function to populate shipping options list
     */
    function populateRadioOptions() {
        //TODO: Use loops instead of hard maps
        //Open rates
        const openCell = document.getElementById("openStandard");
        openCell.value = shippingCosts.open.standard;
        const openFastCell = document.getElementById("openFast");
        openFastCell.value = shippingCosts.open.fast;
        const openNitroCell = document.getElementById("openNitro");
        openNitroCell.value = shippingCosts.open.nitro;
        //Closed rates
        const closedCell = document.getElementById("closedStandard");
        closedCell.value = shippingCosts.closed.standard;
        const closedFastCell = document.getElementById("closedFast");
        closedFastCell.value = shippingCosts.closed.fast;
        const closedNitroCell = document.getElementById("closedNitro");
        closedNitroCell.value = shippingCosts.closed.nitro;

        //Open rates
        const openStandardLabel = document.getElementById("openStandardLabel");
        openStandardLabel.innerHTML += dollarFormat(shippingCosts.open.standard);
        const openFastLabel = document.getElementById("openFastLabel");
        openFastLabel.innerHTML += dollarFormat(shippingCosts.open.fast);
        const openNitroLabel = document.getElementById("openNitroLabel");
        openNitroLabel.innerHTML += dollarFormat(shippingCosts.open.nitro);
        //Closed rates
        const closedStandardLabel = document.getElementById("closedStandardLabel");
        closedStandardLabel.innerHTML += dollarFormat(shippingCosts.closed.standard);
        const closedFastLabel = document.getElementById("closedFastLabel");
        closedFastLabel.innerHTML += dollarFormat(shippingCosts.closed.fast);
        const closedNitroLabel = document.getElementById("closedNitroLabel");
        closedNitroLabel.innerHTML += dollarFormat(shippingCosts.closed.nitro);
    }

    /**
     * Helper function to format the calculated value into dollar format
     * @param {*} calculatedValue
     * @returns
     */
    function dollarFormat(calculatedValue) {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: "USD",
        }).format(calculatedValue);
    }

    /**
     * quick check for empty values
     * @param value
     * @returns {boolean}
     */
    function isEmptyValue(value) {
        return value === "";
    }

    /**
     * Function to capture vehicle details to be included in the quote later
     */
    function captureVehicleDetails() {
        let vehicleValid = validateVehicleDetails();
        let typeValid = validateVehicleTypeAndStatus();
        if (vehicleValid && typeValid) {
            order.year = document.getElementById("year").value;
            order.make = document.getElementById("make").value;
            order.model = document.getElementById("model").value;
            order.vehicleType = document.getElementById("vehicleType").value;
            order.operationalStatus = document.getElementById("operationalStatus").value;
            // Set element visibility
            document.getElementById("output").style.visibility = "visible";
            document.getElementById("vehicleDetails").style.visibility = "hidden";

            // Progress Bar
            document.getElementById("vehicleDetailsStep").classList.remove('isActive');
            document.getElementById("outputStep").classList.add('isActive');
            document.getElementById('instantQuoteAddToCart').style.visibility = "visible";
        }

        // hidden inputs
        const trailerSelection = document.getElementById('trailerSelection');
        const speedSelection = document.getElementById('speedSelection');

        // Add event listeners to radio buttons to set values of hidden inputs
        document.querySelectorAll('.speedOption').forEach((elem) => {
            elem.addEventListener('change', function(event) {
                trailerSelection.value = event.target.dataset.type;
                speedSelection.value = event.target.dataset.speed;
            });
        });
    }

    /**
     * Function to get desired shipping date
     */
    function getDesiredDate() {
        order.desiredShippingDate = document.getElementById("desiredDate").value;
    }

    /**
     * validation function for zip code
     * @returns {boolean}
     */
    function validateZipCodes() {
        let isValid = true;
        const originZip = document.getElementById("origin");
        const destinationZip = document.getElementById("destination");
        const desiredShippingDate = document.getElementById("desiredDate");
        if (isEmptyValue(originZip.value)) {
            originZip.style.border = errorBorder;
            isValid = false;
        }
        if (isEmptyValue(destinationZip.value)) {
            destinationZip.style.border = errorBorder;
            isValid = false;
        }
        if (isEmptyValue(desiredShippingDate.value)) {
            desiredShippingDate.style.border = errorBorder;
            isValid = false
        }

        return isValid;
    }

    /**
     * Validation function for vehicle details
     * @returns {boolean}
     */
    function validateVehicleDetails() {
        let isValid = true;
        const year = document.getElementById("year");
        const make = document.getElementById("make");
        const model = document.getElementById("model");
        if (isEmptyValue(year.value)) {
            isValid = false;
            year.style.border = errorBorder;
        }
        if (isEmptyValue(make.value)) {
            isValid = false;
            make.style.border = errorBorder;
        }
        if (isEmptyValue(model.value)) {
            isValid = false;
            model.style.border = errorBorder;
        }

        return isValid;
    }

    /**
     * Function to validate vehicle type and status
     * @returns {boolean}
     */
    function validateVehicleTypeAndStatus() {
        let isValid = true;
        let invalidSelections = [];
        const type = document.getElementById("vehicleType");
        const operationalStatus = document.getElementById("operationalStatus");
        if (type.value !== "car") {
            isValid = false
            type.style.border = errorBorder;
            if (!isEmptyValue(type.value)) {
                invalidSelections.push("vehicle type");
            }
        }
        if (operationalStatus.value !== "running") {
            isValid = false;
            operationalStatus.style.border = errorBorder;
            if (!isEmptyValue(operationalStatus.value)) {
                invalidSelections.push("operational status");
            }
        }

        if (!isValid && invalidSelections.length > 0) {
            handleInvalidTypes(invalidSelections);
        }

        return isValid;
    }

    /**
     * Function to validate shipping selection on submission before routing to Stripe
     *
     * @returns
     */
    function validateSubmission() {
        let isValid = false;
        if (document.querySelectorAll('.speedOption:checked').length > 0) {
            isValid = true;
        } else {
            alert("No shipping option selected.\nPlease select a trailer and speed option to consider.");
        }

        return isValid;
    }

    /**
     * Function to handle messaging for when an invalid selection is made
     * @param invalidSections
     */
    function handleInvalidTypes(invalidSections) {
        const sectionsLength = invalidSections.length;
        let message = "Based on the ";
        for(let index = 0; index < sectionsLength; index++) {
            message += invalidSections[index];
            if ((index + 1) !== sectionsLength) {
                message += " and ";
            }
        }
        message += " selected, this vehicle requires a custom quote. Please proceed to the contact us page for a custom quote";

        if (window.confirm(message)) {
            window.location.href = "https://offsettransport.com/contact-us/";
        }
    }

    /**
     * Function to handle the submission of the quote
     * to the payment capture system
     */
    function submitQuote() {
        const isValid = validateSubmission();
        if (isValid) {
            order.id = 'IQ-' + Date.now();
            const shippingSelection = document.querySelectorAll('.speedOption:checked')[0];
            order.speedSelection = shippingSelection.dataset.speed;
            order.trailerSelection = shippingSelection.dataset.type;
            order.cost = shippingSelection.value;
            console.log('order is:', order);
            //TODO: Wire up this data to the payment system.
        }
    }

    /**
     * Function to dynamically load the Google Maps script
     */
    function loadMaps() {
        const script = document.createElement("script");
        script.src = googleMapsUrl;
        script.async = true;
        document.head.appendChild(script);
    }

    return {
        calculateDistance: calculateDistance,
        captureVehicleDetails: captureVehicleDetails,
        loadMaps: loadMaps,
        submitQuote: submitQuote
    };
})();

instantQuoteModule.loadMaps();
