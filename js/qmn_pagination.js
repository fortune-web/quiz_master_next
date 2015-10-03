
//Function to validate the answers provided in quiz
function qmnValidatePage() {
	var result = qmnValidation( ".quiz_section.slide"+window.mlw_quiz_slide+' *' );
	return result;
}


//Function to advance quiz to next page
function nextSlide( mlw_pagination, mlw_goto_top ) {
	jQuery( ".quiz_section" ).hide();
	for ( var i = 0; i < mlw_pagination; i++ ) {
		if (i == 0 && window.mlw_previous == 1 && window.mlw_quiz_slide > 1) {
			window.mlw_quiz_slide = window.mlw_quiz_slide + mlw_pagination;
		} else {
			window.mlw_quiz_slide++;
		}
		if (window.mlw_quiz_slide < 1) {
			window.mlw_quiz_slide = 1;
		}
		jQuery( ".mlw_qmn_quiz_link.mlw_previous" ).hide();

    if (firstPage) {
      if (window.mlw_quiz_slide > 1) {
				jQuery( ".mlw_qmn_quiz_link.mlw_previous" ).show();
      }
    } else {
			if (window.mlw_quiz_slide > mlw_pagination) {
				jQuery( ".mlw_qmn_quiz_link.mlw_previous" ).show();
			}
    }
		if (window.mlw_quiz_slide == qmn_section_total) {
			jQuery( ".mlw_qmn_quiz_link.mlw_next" ).hide();
		}
		if (window.mlw_quiz_slide < qmn_section_total) {
			jQuery( ".mlw_qmn_quiz_link.mlw_next" ).show();
		}
		jQuery( ".quiz_section.slide"+window.mlw_quiz_slide ).show();
	}

	//Update page number
	qmn_current_page += 1;
	jQuery(".qmn_page_counter_message").text(qmn_current_page+"/"+qmn_total_pages);

	window.mlw_previous = 0;
	if (mlw_goto_top == 1) {
		qmnReturnToTop();
	}
}

function prevSlide(mlw_pagination, mlw_goto_top) {
	jQuery( ".quiz_section" ).hide();
	for (var i = 0; i < mlw_pagination; i++) {
		if (i == 0 && window.mlw_previous == 0)	{
			window.mlw_quiz_slide = window.mlw_quiz_slide - mlw_pagination;
		} else {
			window.mlw_quiz_slide--;
		}
		if (window.mlw_quiz_slide < 1) {
			window.mlw_quiz_slide = 1;
		}

		jQuery( ".mlw_qmn_quiz_link.mlw_previous" ).hide();

		if (firstPage) {
			if (window.mlw_quiz_slide > 1) {
				jQuery( ".mlw_qmn_quiz_link.mlw_previous" ).show();
			}
		} else {
			if (window.mlw_quiz_slide > mlw_pagination) {
				jQuery( ".mlw_qmn_quiz_link.mlw_previous" ).show();
			}
		}
		if (window.mlw_quiz_slide == qmn_section_total) {
			jQuery( ".mlw_qmn_quiz_link.mlw_next" ).hide();
		}
		if (window.mlw_quiz_slide < qmn_section_total) {
			jQuery( ".mlw_qmn_quiz_link.mlw_next" ).show();
		}
		jQuery( ".quiz_section.slide"+window.mlw_quiz_slide ).show();
	}

	//Update page number
	qmn_current_page -= 1;
	jQuery(".qmn_page_counter_message").text(qmn_current_page+"/"+qmn_total_pages);

	window.mlw_previous = 1;
	if (mlw_goto_top == 1) {
		qmnReturnToTop();
	}
}

function qmnReturnToTop() {
	jQuery('html, body').animate({scrollTop: jQuery('.mlw_qmn_quiz').offset().top - 100}, 1000);
}

jQuery( ".quiz_section" ).hide();
jQuery( ".quiz_section" ).append( "<br />" );
jQuery( '.mlw_qmn_quiz' ).append( '<div class="qmn_pagination border margin-bottom"></div>' );
jQuery( ".qmn_pagination" ).append( '<a class="qmn_btn mlw_qmn_quiz_link mlw_previous" href="#">'+qmn_pagination_previous_text+'</a>' );
jQuery( ".qmn_pagination" ).append( '<span class="qmn_page_message"></span>' );
jQuery( ".qmn_pagination" ).append( '<div class="qmn_page_counter_message"></div>' );
jQuery( ".qmn_pagination" ).append( '<a class="qmn_btn mlw_qmn_quiz_link mlw_next" href="#">'+qmn_pagination_next_text+'</a>' );
window.mlw_quiz_slide = 0;
window.mlw_previous = 0;

//
var qmn_current_page = 0;
var qmn_section_total = qmn_total_questions + 1;
if (qmn_section_comments == 0) {
	qmn_section_total += 1;
}
var qmn_total_pages = Math.ceil(qmn_section_total/qmn_pagination);
if (firstPage) {
	qmn_total_pages += 1;
	qmn_section_total += 1;
}

if (firstPage) {
  nextSlide(1, 0);
} else {
  nextSlide(qmn_pagination, 0);
}

jQuery(".mlw_next").click(function(event) {
	event.preventDefault();
	if ( qmnValidatePage() ) {
		nextSlide(qmn_pagination, 1);
	}
});

jQuery(".mlw_previous").click(function(event) {
	event.preventDefault();
	prevSlide(qmn_pagination, 1);
});
