$(document).ready(function() {	
    $(function(){
         $('#uploadForm').submit(function(){
              $('<input type="hidden" name="javascript" value="yes" />').appendTo($(this));
              
                var iframeName = ('iframeUpload');
                var iframeTemp = $('<iframe name="'+iframeName+'" src="about:blank" />');
                iframeTemp.css('display', 'none');
                
                $('body').append(iframeTemp);
                
                /* submit the uploadForm */
                $(this).attr({				
                    action: 'processUpload.php',
                    method: 'post',
                    enctype: 'multipart/form-data',
                    encoding: 'multipart/form-data',
                    target: iframeName
                });
                
                setTimeout(function(){
                    iframeTemp.remove();
                    $('input[name="javascript"]').remove();
                    inputLength = 0;
                    inputLength += $('input[name="upload"]').val().length;
                    if(0 < inputLength){
                        $('body').append('<div id="ty" class="thankyouModal"><h3>Thank You! Your upload is complete...</h3></div>');
                        var modalMarginTop = ($('#ty').height() + 60) / 2;
                        var modalMarginLeft = ($('#ty').width() + 60) / 2;
                        /* apply the margins to the modal window */
                        $('#ty').css({
                            'margin-top'    : -modalMarginTop,
                            'margin-left'     : -modalMarginLeft
                        });
                        $('.thankyouModal').fadeIn('slow', function(){
                        $('input[name="upload"]').val('');
                        $(this).fadeOut(1500, function() {
                                                       $(this).remove();
                        });
                    });
                };
         }, 1000);
        });
    });
});

