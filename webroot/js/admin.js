var dataset_form = {
	category_selects_wrapper: null,
	
	loc_id_select: null,
	
	setup: function() {
		this.loc_id_select = $('#DatasetLocationId');
		this.loc_id_select.hide();
		
		this.category_selects_wrapper = $('#category_selects');
		this.category_selects_wrapper.children('div').hide();
		
		$('#loc_type_selection').change(function() {
			dataset_form.onLocTypeSelect();
		});
		this.onLocTypeSelect();
		
		$('select.frequency').change(function () {
			dataset_form.onFrequencySelect();
		});
		this.onFrequencySelect();
	},
	
	onLocTypeSelect: function() {
		var loc_type = $('#loc_type_selection').val();
		this.loc_id_select.show();
		this.loc_id_select.find('optgroup').each(function() {
			var optgroup = $(this);
			if (optgroup.attr('label') == loc_type) {
				optgroup.show();
				optgroup.find('option').first().prop('selected', true);
			} else {
				optgroup.hide();
			}
		});
		
		// Remember what frequency was already selected
		var selected_freq = this.category_selects_wrapper.children(':visible').children('select.frequency').val();
		
		// Hide and disable all frequency/category <select>s
		var subwrappers = this.category_selects_wrapper.children('div');
		subwrappers.hide();
		subwrappers.children('select').prop('disabled', true);
		
		// Show and enable the appropriate <select>s
		var categories_wrapper = $('#categories_'+loc_type);
		categories_wrapper.show();
		categories_wrapper.children(':disabled').prop('disabled', false);
		
		// Get the visible frequency selection
		var freq_select = this.category_selects_wrapper.children(':visible').children('select.frequency');
		
		// If the same frequency can be selected, do so 
		var matching_freq = freq_select.children('option[value=\"'+selected_freq+'\"]');
		if (matching_freq.length) {
			matching_freq.first().prop('selected', true);
			this.onFrequencySelect();
		}
	},
	
	onFrequencySelect: function() {
		var freq_select = this.category_selects_wrapper.children(':visible').children('select.frequency');
		var freq = freq_select.val();
		var category_select = freq_select.next('select');
		category_select.find('optgroup').each(function() {
			var optgroup = $(this);
			if (optgroup.attr('label') == freq) {
				optgroup.show();
				optgroup.find('option').first().prop('selected', true);
			} else {
				optgroup.hide();
			}
		});
	}
};

var release_form = {
	setup: function() {
		// Hide and disable all frequency and category <select>s
		$('#frequency_selects > div, #category_selects > div').hide();
		$('#frequency_selects select:enabled, #category_selects select:enabled').prop('disabled', true);
		
		// Show a frequency selection and a category selection according to the selected loc type
		this.onLocTypeSelect();
		
		$('#loc_type_selection').change(function() {
			release_form.onLocTypeSelect();
		});
		
		$('select.frequency').change(function () {
			release_form.onFrequencySelect();
		});
	},
	
	onLocTypeSelect: function() {
		// Remember what frequency was already selected
		var selected_freq = $('#frequency_selects select:enabled').val();
		
		// Hide and disable all frequency and category <select>s
		$('#frequency_selects > div, #category_selects > div').hide();
		$('#frequency_selects select:enabled, #category_selects select:enabled').prop('disabled', true);
		
		// Show and enable the appropriate frequency selection
		var loc_type = $('#loc_type_selection').val();
		var frequency_select = $('#frequency_select_'+loc_type+' select');
		frequency_select.prop('disabled', false);
		$('#frequency_select_'+loc_type).show();
		
		// Attempt to select the same frequency that was previously selected
		frequency_select.val(selected_freq);
		
		// Show and enable the appropriate category selection
		var frequency = frequency_select.val().replace(' ', '_');
		var category_container = $('#categories_'+loc_type+'_'+frequency);
		category_container.find('select').prop('disabled', false);
		category_container.show();
	},
	
	onFrequencySelect: function() {
		// Hide and disable all categories
		$('#category_selects > div').hide();
		$('#category_selects select:enabled').prop('disabled', true);
		
		// Show and enable the appropriate category selection
		var loc_type = $('#loc_type_selection').val();
		var frequency = $('#frequency_select_'+loc_type+' select').val().replace(' ', '_');
		var category_select_id = '#categories_'+loc_type+'_'+frequency;
		$(category_select_id+' select').prop('disabled', false);
		$(category_select_id).show();
	},
	
	selectCategory: function(category_id) {
		// Hide and disable all frequency and category <select>s
		$('#frequency_selects > div, #category_selects > div').hide();
		$('#frequency_selects select:enabled, #category_selects select:enabled').prop('disabled', true);
		
		// Enable, show, and select category
		var cat_select = $('#category_selects option[value='+category_id+']').parent('select');
		if (cat_select.length == 0) {
			alert('Cannot preselect category #'+category_id+'. Category not found.');
			return;
		}
		cat_select.val(category_id).prop('disabled', false);
		var cat_container = cat_select.parent('div');
		cat_container.show();
		var cat_info = cat_container.attr('id').split('_');
		var freq_id = cat_info.pop();
		var loc_type_id = cat_info.pop();
		
		// Enable, show, and select frequency
		var frequency_select = $('#frequency_select_'+loc_type_id+' select');
		frequency_select.prop('disabled', false);
		frequency_select.val(freq_id);
		$('#frequency_select_'+loc_type_id).show();
		
		// Select location type
		$('#loc_type_selection').val(loc_type_id);
	}
};