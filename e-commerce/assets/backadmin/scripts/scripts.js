CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
CKEDITOR.config.entities = false;
$("#update_purchase_btn").on("click", function (event) {

  event.preventDefault();
  let loader = $("#loader-site");
  var vidFileLength = $("input[type=file]")[0].files.length;
  $("#invalidFile").addClass("d-none");
  if (vidFileLength != 0) {
    loader.show().css("background-color", "");
    let form = $('#purchase_upload_form')[0];
    $.ajax({
      url: 'update-purchase',
      data: new FormData(form),
      type: 'POST',
      async: true,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (response) {
        if (response.status === "200") {
          loader.hide();
          $('input[type="file"]').val("");
        } else {
          loader.show().css("background-color", "red");
          if (response.invalidTranslation !== undefined) {
            loader.hide();
            $("#invalidFile").removeClass("d-none");
          } else {
            $("#invalidFile").addClass("d-none");
          }
        }
      }
    });
  }
});
$('#getExtractLead').on('click', function () {
   if ($("#record-id").val() == '') {
        if (($("#datepickerdebut").val() == '') ||
            ($("#datepickerfin").val() == '')){

          $("#overlay .modal-body").text("Merci de bien remplir les champs");
          $('#overlay').fadeIn(300);

                 return false;
         }
        else if(Date.parse($("#datepickerdebut").val()) > Date.parse($("#datepickerfin").val())){

          $("#overlay .modal-body").text("Veuillez sélectionner une autre date de fin");
          $('#overlay').fadeIn(300);

         return false;
         }

          }
    });
$("#update_translation_btn").on("click", function (event) {
  event.preventDefault();
  let loader = $("#loader-site");
  var vidFileLength = $("input[type=file]")[0].files.length;
  $( "#invalidFile" ).addClass( "d-none" );
  if (vidFileLength != 0) {
    loader.show().css("background-color", "");
    let form = $('#translation_upload_form')[0];
    $.ajax({
      url: 'update-translation',
      data: new FormData(form),
      type: 'POST',
      async: true,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (response) {
        if (response.status === "200") {
          loader.hide();
          $('input[type="file"]').val("");
        } else {
          loader.show().css("background-color", "red");
          if(response.invalidTranslation !== undefined){
            loader.hide();
            $( "#invalidFile" ).removeClass( "d-none" );
          }else{
            $( "#invalidFile" ).addClass( "d-none" )
          }
        }
      },
    });
  }
});
$("#update_dealer_btn").on("click", function (event) {
  event.preventDefault();
  let loader = $("#loader-site");
  var vidFileLength = $("input[type=file]")[0].files.length;
  $("#invalidFile").addClass("d-none");
  if (vidFileLength != 0) {
    loader.show().css("background-color", "");
    let form = $('#dealer_upload_form')[0];
    $.ajax({
      url: 'update-dealer',
      data: new FormData(form),
      type: 'POST',
      async: true,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (response) {
        if (response.status === "200") {
          loader.hide();
          $('input[type="file"]').val("");
        } else {
          loader.show().css("background-color", "red");
          if (response.invalidTranslation !== undefined) {
            loader.hide();
            $("#invalidFile").removeClass("d-none");
          } else {
            $("#invalidFile").addClass("d-none");
          }
        }
      }
    });
  }
});
$('#close').click(function() {
  $('#overlay').fadeOut(300);
});
$("#searchTranslation").on("click", function () {

  $.ajax({
    url: 'search-translation',
    type: 'POST',
    data: {
      'label': $('input[name=likeLabel]').val(),
      'page': $('input[name=likePage]').val(),
      'value': $('input[name=likeValue]').val()
    }, success: function (data) {

      $("#showTranslationDiv").empty();

      var response = JSON.parse(data);
      if (response.status === "200") {
        if (response.content.length !== 0) {
          var answerSpan = '<div class="card-body"><h5 class="card-title">Résultat de votre recherche</h5>\n' + '<table class="table" id="tab">';

          answerSpan += '<tr><th><div class="col-md-3">PAGE</div></th>';
          answerSpan += '<th><div class="col-md-3">LABEL</div></th>';
          answerSpan += '<th><div class="col-md-3">VALUE</div></th>';
          answerSpan += '<th><div class="col-md-3"></div></th></tr>';

          $.each(response.content, function (i, item) {
            answerSpan += '<tr><td><div class="col-md-3" id="translationPage' + i + '">' + item.page + '</div></td>';
            answerSpan += '<td><div class="col-md-3" id="translationLabel' + i + '">' + item.label + '</div></td>';
            answerSpan += '<td><div class="col-md-3" id="translationValue' + item.id + '">' + item.value + '</div></td>';
            answerSpan += '<td><div class="col-md-3">';
            answerSpan += '<button id="edit' + item.id + '" type="button"  class="btn mr-2 mb-2 btn-primary edit" ';
            answerSpan += 'data-toggle="modal" data-target="#myModal"  data-translationid="' + item.id + '" ';
            answerSpan += 'data-page="' + item.page + '" data-title="' + item.label + '" ';
            answerSpan += 'data-content=\'' + item.value + '\'>Modifier</button>';
            answerSpan += '</div></td></tr>';
          });
          answerSpan += '</table>\n' + '</div>\n' + '\n';
          $("#showTranslationDiv").append(answerSpan);

        } else {
          $("#overlay .modal-body").text("Résultat non trouvé dans votre recherche");
          $('#overlay').fadeIn(300);
        }
      } else {
        $("#overlay .modal-body").text("error get translation");
        $('#overlay').fadeIn(300);
      }
    },
    error: function () {
      $("#overlay .modal-body").text("error get translation");
      $('#overlay').fadeIn(300);
    }
  });
});
$('body').on('click', '.edit', function () {
  var btn = $(this);
  var title = $(this).data('title');
  var page = $(this).data('page');
  var Id = $(this).data('translationid');
  var content = $(this).data('content');

  $("#myModal .modal-body input").val( Id );
  $("#myModal .modal-header h5").text( page );
  $("#myModal .modal-body p").text( title );
  $("#myModal .modal-body textarea").html(CKEDITOR.instances['textarea'].setData(content));
});



$('body').on('click', '#update-search-btn', function () {
  var position =  $('input[id=translationid]').val();
  $('#loader-translation-update' + position).show().css("background-color", "");
  $('#myModal #invalidTranslation').remove();
  $.ajax({
    url: 'update-translation-one',
    type: 'POST',
    data: {
      id: position,
      value: CKEDITOR.instances['textarea'].getData().trim()
    },
    success: function (data) {
      var response = JSON.parse(data);
      $('#translationValue'+position).html(response.value);
      $('#edit'+ position).data('content',response.value);
    }
  }).done(function (response) {
    $('.modal-open .close').click();
  });;

});
