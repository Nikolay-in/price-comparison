$(document).ready(function() {
    //Checkboxes
    $('.checkBoxWord').click(function(){
        productId = this.id.split('-')[1];
        wordId = this.id.split('-')[2];
        word = $('#label-' + productId + '-' + wordId).text().trim();
        wordsArr = $('input[id="hiddenModels[' + productId + ']').val().split(';');
        wordsArr = wordsArr.filter(el => el != '');
        if($(this).is(':checked')){
            wordsArr.push(word);
        } else {
            index = wordsArr.indexOf(word);
            if (index != -1) {
                wordsArr.splice(index, 1);
            }
        }
        $('input[id="hiddenModels[' + productId + ']"').val(wordsArr.join(';'));
        if ($('input[id="hiddenRadio[' + productId + ']"').val()  != '') {
            word = $('input[id="hiddenRadio[' + productId + ']"').val();
            wordsArr.unshift(word);
        }
        $('input[id="models[' + productId + ']"').val(wordsArr.join('-'));
    });
    //Radio buttons
    $('.radioWord').click(function(){
        productId = this.id.split('-')[1];
        wordId = this.id.split('-')[2];
        word = $('#label-' + productId + '-' + wordId).text().trim();
        wordsArr = $('input[id="hiddenModels[' + productId + ']').val().split(';');
        wordsArr = wordsArr.filter(el => el != '');
        if (word != 'none') {
            $('input[id="hiddenRadio[' + productId + ']"').val(word);
            wordsArr.unshift(word);
        } else {
            $('input[id="hiddenRadio[' + productId + ']"').val('');
        }
        $('input[id="models[' + productId + ']"').val(wordsArr.join('-'));
    });

    //True scrolling to item
    $(".scrollToNext").click(function(event){
        event.preventDefault();
        var o =  $( $(this).attr("href") ).offset();   
        var sT = o.top - $("#navbar").outerHeight(true); 
        window.scrollTo(0,sT);
    });

    //Checkbox click
    $(".checkList").click(function(){
        let word = $(this).attr('word');
        if ($('#checkListWrapper').css('display') !== 'none' && $('#clModel').val() !== '') {
            word = $('#clModel').val() + '-' + word;
        }
        $("#brandName").text($(this).attr('brandname'));
        $("#clModel").val(word);
        $("#clModelApply").val(word);
        $("#clBrandId").val($(this).attr('brandid'));
        updateCheckList($(this).attr('prodid'));
    });

    //Resync button
    $("#resync").click(function() {
        updateCheckList();
    });

    //Refresh on input change
    $("#clModel").keyup(function() {
        $("#clModelApply").val($(this).val());
        updateCheckList();
    });

    //Hide model strip
    $("#hideStrip").click(function(){
        updateCheckList();
    });

    //Update Check List
    function updateCheckList(id = null) {
        let url = '/admin/checklist.php?brandId=' + $('#clBrandId').val() + '&word=' + encodeURIComponent($("#clModel").val());
        if ($("#hideStrip").prop('checked')) {
            url += '&hideStrip=1';
        }
        $.ajax({
            url: url, 
            success: function(result){
                $("#checkList").html(result);
                $("#checkListWrapper").css({"display": "block", "position": "fixed", "top": "0px"});
                
                //If the result is a single product check it
                if ($('.cl-cbox').length == 1) {
                    $('.cl-cbox').prop('checked', true);
                    $('.cl-cbox').parent().parent().parent().addClass('bg-success text-dark bg-opacity-10');
                    $("#selected").text('1');
                } else {
                    $('input[value=' + id + ']').prop('checked', true);
                    $('input[value=' + id + ']').parent().parent().parent().addClass('bg-success text-dark bg-opacity-10');
                    $("#selected").text('1');
                }
                submitButtonState();

                //tr click toggles checkbox
                $('tr').click(function(e){
                    if ( !$(e.target).is("a") && !$(e.target).hasClass('model')) {
                        $(this).find('.cl-cbox').prop('checked', !$(this).find('.cl-cbox').prop('checked'));
                        let counter = parseInt($("#selected").text());
                        if ($(this).find('.cl-cbox').prop('checked') === true) {
                            $(this).addClass('bg-success text-dark bg-opacity-10');
                            counter++;
                        } else {
                            $(this).removeClass('bg-success text-dark bg-opacity-10');
                            counter--;
                        }
                        $("#selected").text(counter);
                    }
                    submitButtonState();
                });

                //Model Strip Click
                $('.model').click(function(){
                    $('#clModelApply').val($(this).text());
                });
            }
        });
    }

    //Div hiding
    $('div#layoutSidenav').click(function(e){
        //if ($('#checkListWrapper').css('display') !== 'none' && $(this).attr('id') !== 'checkListWrapper' && $(this).attr('id') !== 'controls' && $(this).attr('id') !== 'checkList') {
        if ($('#checkListWrapper').css('display') !== 'none' && !$(e.target).is('svg')  && !$(e.target).is('path')) {
            $('#checkListWrapper').css('display', 'none');
            $("#hideStrip").prop('checked', false);
        }
    });

    //Toggle all checklist boxes
    $('#toggleClBoxes').click(function(){
        $('.cl-cbox').prop('checked', !$('.cl-cbox').prop('checked'));   
        if ($('.cl-cbox').prop('checked') == true) {
            $('.cl-cbox').parent().parent().parent().addClass('bg-success text-dark bg-opacity-10');
            $("#selected").text($('.cl-cbox').length);
        } else {
            $('.cl-cbox').parent().parent().parent().removeClass('bg-success text-dark bg-opacity-10');
            $("#selected").text('0');
        }
        submitButtonState();
    });

    //Enable / disable submit button
    function submitButtonState() {
        if ($('.cl-cbox:checked').length == 0) {
            $('#submitButton').prop('disabled', true);
        } else {
            $('#submitButton').prop('disabled', false);
        }
    }

    document.addEventListener('keypress', (e) => {
        if (document.getElementById('checkListWrapper').style.display = 'block' && e.target.tagName !== 'INPUT') {
            if (e.key == 'a') {
                document.getElementById('toggleClBoxes').click();
            }
            if (e.key == 's') {
                document.getElementById('submitButton').click();
            }
        }
    })
});

function clearAll(id) {
    document.getElementById(`models[${id}]`).value='';
    document.getElementById(`hiddenModels[${id}]`).value='';
    for (el of document.querySelectorAll(`[id^="checkBox-${id}-"]`)) {
        el.checked = false; 
    }
    document.getElementById(`radio-${id}-none`).checked = true;
}

