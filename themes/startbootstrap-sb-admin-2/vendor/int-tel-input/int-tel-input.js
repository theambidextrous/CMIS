// JavaScript Document
//alert( iti_object_url.iti_object_url );
jQuery(function($) {		
	jQuery("#phonenumber").intlTelInput({		
		autoPlaceholder: false,
		defaultCountry: "auto",
		 geoIpLookup: function(callback) {
		   jQuery.get('http://ipinfo.io', function() {}, "jsonp").always(function(resp) {
			 var countryCode = (resp && resp.country) ? resp.country : "";
			 callback(countryCode);
		   });
		 },
		nationalMode: false,
		preferredCountries: ['ke', 'ug', 'tz']
	});
});