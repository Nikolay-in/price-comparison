$(document).ready(function() {
    //Checkboxes
    $('.cb-img').click(function(){
        let width = 0;
        let margin = '';
        if($(this).is(':checked')){
            if (document.getElementById("productImages").children.length == 0) {
                width = 100;
                margin = 'mb-2';
                
            } else {
                width = 47;
                margin = 'mx-1';
            }
            let childImage = '';
            if (this.value.endsWith('-orig')) {
                let imgname = this.value.slice(0, -5);
                childImage = '<a href="#" id="' + this.name + '" class="pop"><img src="/images/' + imgname + '" class="' + margin + '" width="' + width + '%" style="position: relative;"></a>';
            } else {
                childImage = '<a href="#" id="' + this.name + '" class="pop"><img src="/images/temp/' + this.value + '" class="' + margin + '" width="' + width + '%" style="position: relative;"></a>';
            }
            $('#productImages').append(childImage);
            let images = [];
            if ($('#imagesInput').val() != '') { images.push($('#imagesInput').val().split(',')); }
            images.push(this.value);
            $('#imagesInput').val(images.join(','));
            $(this).parent().parent().addClass('bg-success text-dark bg-opacity-25');
            //Pop Image
            $(function() {
                $('.pop').on('click', function() {
                    $('.imagepreview').attr('src', $(this).find('img').attr('src'));
                    $('#imagemodal').modal('show'); 
                });		
            });
        } else {
            $('#productImages').find('#' + this.name).remove();
            let images = $('#imagesInput').val().split(',');
            let i = images.indexOf(this.value);
            images.splice(i, 1);
            images = images.join(',');
            $('#imagesInput').val(images);
            $(this).parent().parent().removeClass('bg-success text-dark bg-opacity-25');
        }
    });

    $('input.cb-offer').click(function(){
        let ids = ($('input#offers').val() != '') ? $('input#offers').val().split(',') : [];
        let i = ids.indexOf($(this).val());
        if ($(this).is(':checked') && i == -1){
            ids.push($(this).val());
            $(this).parent().parent().addClass('bg-success text-dark bg-opacity-25');
        } else {
            if (i != -1) {
                ids.splice(i, 1);
            }
            $(this).parent().parent().removeClass('bg-success text-dark bg-opacity-25');
        }
        ids = ids.join(',');
        $('input#offers').val(ids);
    });

    //Clear images button
    $('#clearImages').click(function(){
        $('.cb-img').prop('checked', false);
        $('.cb-img').parent().parent().removeClass('bg-success p-2 text-dark bg-opacity-25');
        $('#productImages').children().remove();
        $('#imagesInput').val('');
    });

    //Pop Image
    $(function() {
        $('.expand').on('click', function() {
            $('.imagepreview').attr('src', $(this).parent().find('img').attr('src'));
            $('#imagemodal').modal('show'); 
        });		
    });

    //Radio buttons
    $('input:checked').each(function() {
        $(this).parent().parent().addClass('bg-success text-dark bg-opacity-25');
    });

    $('input[type="radio"]').click(function(){
        $(this).parent().parent().addClass('bg-success text-dark bg-opacity-25');
        $('input[type="radio"]:not(:checked)').parent().parent().removeClass('bg-success text-dark bg-opacity-25');
    });

    $('.radioTitle').click(function(){
        $('input#title').val($(this).val());
    }); 

    $('.radioEAN').click(function(){
        $('span#ean').text($(this).parent().find('label').text());
        $('input#eanInput').val($(this).val());
    });

    $('.radioModel').click(function(){
        $('span#model').text($(this).parent().find('label').text());
        $('input#modelInput').val($(this).val());
    }); 

    $('.radioCat').click(function(){
        $('span#category').html($(this).parent().find('label').html());
        $('input#subCatInput').val($(this).val());
    });

    $('.radioDesc').click(function(){
        $('textarea#description').val($(this).parent().find('label').text());
    });
});