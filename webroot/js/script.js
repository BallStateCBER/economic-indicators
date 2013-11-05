function prepareSidebar() {
	$('.menu_section a.group').click(function(event) {
		event.preventDefault();
		$(this).next('.categories_in_group').slideToggle(300);
	});
}

var indicatorsSearch = {
	results: null,			// Initially loaded with all possible results
	datasetViewUrl: null,	// URL to view a dataset, will have the dataset ID appended
	locationTypes: [],
	
	prepareSelect2: function () {
		$("#sidebar_category_select").select2({
			data: indicatorsSearch.results,
			width: '180px',
			placeholder: 'Select or start typing...',
			matcher: function (term, text, option) {
				var matchingLocationTypeIds = [];
				
				// For each location name => location type ID pair provided
				for (locationName in indicatorsSearch.locationTypes) {
					var locationTypeId = indicatorsSearch.locationTypes[locationName];
					
					// Mark this location's type as matching the search term
					var matchPosition = term.toUpperCase().indexOf(locationName.toUpperCase());
					if (matchPosition >= 0) {
						if (matchingLocationTypeIds.indexOf(locationTypeId) == -1) {
							matchingLocationTypeIds.push(locationTypeId);
						}
						
						// Remove the location name from the term 
						if (matchPosition == 0) {
							term = term.substr(locationName.length);
						} else {
							term = term.substr(0, matchPosition - 1)+term.substr(matchPosition + locationName.length);
						}
						term = term.trim();
					}
				}
				
				// If the search term matches one or more location types
				if (matchingLocationTypeIds.length > 0 && option.hasOwnProperty('location_type_id')) {
					// Remove options that don't match the indicated location types
					if (matchingLocationTypeIds.indexOf(option.location_type_id) == -1) {
						return false;
					}
				}
				
				// Filter by the remaining term normally
				return text.toUpperCase().indexOf(term.toUpperCase()) >= 0;
				
			}
		});
		$("#sidebar_category_select").on('change', function() {
			var loadingImg = $('<img src="/data_center/img/loading_small.gif" alt="Loading" class="loading" />');
			$('#s2id_sidebar_category_select').after(loadingImg);
			window.location.href = indicatorsSearch.datasetViewUrl+'/'+$(this).val();
		});
	},
	
	getMatchingLocationTypeIds: function (term) {
		var matchingLocationTypeIds = [];
		
		// For each location name => location type ID pair provided
		for (locationName in indicatorsSearch.locationTypes) {
			var locationTypeId = indicatorsSearch.locationTypes[locationName];
			
			// Skip checking for a match for this location if this location's type already matches 
			if (matchingLocationTypeIds.indexOf(locationTypeId) >= 0) {
				continue;
			}
			
			// Mark this location's type as matching the search term
			if (locationName.toUpperCase().indexOf(term.toUpperCase()) >= 0) {
				matchingLocationTypeIds.push(locationTypeId);
			}
		}
		return matchingLocationTypeIds;
	}
};

function setupReleaseCalendar(release_dates) {
	var d = new Date();
	var this_year = d.getFullYear();
	var this_month = d.getMonth();
	var beginning_of_month = new Date(this_year, this_month, 1);
	
	if (release_dates.max_date) {
		var last_date_split = release_dates.max_date.split('-');
		var last_date = new Date(last_date_split[0], last_date_split[1] - 1, last_date_split[2]);
	} else {
		var last_date = null;
	}
	
	if (release_dates.min_date) {
		var first_date_split = release_dates.min_date.split('-');
		var first_date = new Date(first_date_split[0], first_date_split[1] - 1, first_date_split[2]);
	} else {
		var first_date = null;
	}
	
	var tooltip_options = {
		items: '.has_releases',
		tooltipClass: 'release_date_tooltip',
		position: {
			my: 'left bottom',
			at: 'right top'
		},
		content: function() {
			var element = $(this);
			var date = element.attr('title');
			if (release_dates.hasOwnProperty(date)) {
				var tooltip = '<ul>';
				for (var i = 0; i < release_dates[date].length; i++) {
					var loc = release_dates[date][i]['location_type_name'];
					var cat = release_dates[date][i]['category'];
					tooltip += '<li><strong>'+cat+'</strong> <span class="loc_type">'+loc+'</span></li>';
				}
				tooltip += '</ul>';
				return tooltip;
			} else {
				return null;
			}
		},
		show: {
			duration: 0
		},
		hide: {
			delay: 5000 // 5 seconds
		},
		open: function (event, ui) {
			var id = ui.tooltip[0]['id'];
			$('.release_date_tooltip').each(function() {
				if (this.id != id) {
					$(this).remove();
				}
			});
		}
	};
	
	$('#release_calendar').datepicker({
		minDate: first_date,
		maxDate: last_date,
		
		hideIfNoPrevNext: true,
		
		prevText: '&larr;',
		nextText: '&rarr;',
		
		beforeShowDay: function(date) {
			var day = date.getDate().toString();
			if (day < 10) {
				day = '0'+day.toString();
			}
			// Because they're zero-indexed for some reason
			var month = (date.getMonth() + 1).toString();
			if (month < 10) {
				month = '0'+month;
			}
			var year = date.getFullYear().toString();
			var formatted_date = year+'-'+month+'-'+day;
			if (release_dates.hasOwnProperty(formatted_date)) {
				var class_name = 'has_releases';
				var tooltip = year+'-'+month+'-'+day;
				var selectable = true;
			} else {
				var class_name = 'no_releases';
				var tooltip = null;
				var selectable = false;
			}
			return [selectable, class_name, tooltip];
		},
		
		onChangeMonthYear: function() {
			$(this).tooltip(tooltip_options);
			$(this).find('tbody a').click(function (event) {
				event.preventDefault();
			});
		}
	}).tooltip(tooltip_options).append('');
}