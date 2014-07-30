$(document).ready(function() {
$('#portfolio li').each(function() {
	$(this).addClass('show');
});
	$('ul#filter a').click(function() {
		$(this).css('outline','none');
		$('ul#filter .current').removeClass('current');
		$(this).parent().addClass('current');
		
		var filterVal = $(this).text().toLowerCase().replace(' ','-');
				
		if(filterVal == 'all') {
			var count=0;
			$('ul#portfolio li.hidden').fadeIn('slow').removeClass('hidden');
			$('#portfolio li').each(function() {
				$(this).addClass('show');
			});
			count=$("#portfolio li .show");
		} else {
			var count=0;
			$('ul#portfolio li').each(function() {
				if(!$(this).hasClass(filterVal)) {
					$(this).fadeOut('normal').addClass('hidden');
					$(this).removeClass('show');
				} else {
					$(this).fadeIn('slow').removeClass('hidden');
					$(this).addClass('show');
					count++;
				}
			});
		}
		paginates(count);
		return false;
	});
});
