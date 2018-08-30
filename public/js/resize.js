resizeGal = function() {
	var screenWidth = $('body').width(),
		imgWidth,  imgHeight,
		imgWidthc, imgHeightc = 180,
		totLargeur = 0,
		newRatio = 1,
		indexRow = 1,
		img = [];

	$('#affsize').text(screenWidth);
	xx = $('a.imaglink');
	xx.unwrap("div[class*='imagrow']");
	xx.removeClass();
	xx.addClass('imaglink');

	// Enumération des anchor
	xx.each(function (index) {
		im = $(this).children('img');
		imgWidth   = im[0].naturalWidth,
		imgHeight  = im[0].naturalHeight,
		imgWidthc  = imgWidth * imgHeightc / imgHeight,
		Sans = totLargeur,
		Avec = totLargeur + imgWidthc,
		depSans = Math.abs(screenWidth - Sans),
		depAvec = Math.abs(screenWidth - Avec);
		if (Avec < screenWidth) {
			img.push(this);
			totLargeur += imgWidthc;
		} else {
			if (depSans >= depAvec) {
				img.push(this);
				totLargeur += imgWidthc;
			}
			newRatio = screenWidth / totLargeur;
			firstIm = true;
			img.forEach(function(item) {
				$(item).addClass("imagrow_"+indexRow);
				im = $(item).children('img');
				percent = (imgWidthc / totLargeur)*100.0;
				$(im[0]).attr('height', imgHeightc*newRatio);
				$(item).attr('width',percent+'%');
			});
			totLargeur = 0;
			img = [];
			if (depSans < depAvec) {
				img.push(this);
				totLargeur += imgWidthc;
			}
			$("a.imagrow_"+indexRow).wrapAll("<div class=\"imagrow\"></div>");
			indexRow++;
		}
	});
	if (img.length != 0) {
		img.forEach(function(item) {
			$(item).addClass("imagrow_"+indexRow);
			im = $(item).children('img');
			percent = (imgWidthc / totLargeur)*100.0;
			$(im[0]).attr('height',imgHeightc*newRatio);
			$(item).attr('width',percent+'%');
		});
		$("a.imagrow_"+indexRow).wrapAll("<div class=\"imagrow\"></div>");
	}
};
$( window ).resize(resizeGal);
//$( window ).ready (resizeGal);
window.addEventListener("load", function(event) {
    resizeGal();
});
