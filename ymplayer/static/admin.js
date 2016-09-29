$(document).ready(function() {
  if ($('#wmd-button-row').length) {
    $('#wmd-button-row').append('<li class="wmd-button" id="wmd-music-button" title="音乐 - Alt+M"><span></span></li>');
    $('#wmd-music-button').click(function() {
      showDialog();
    });
  }

  $('#ymplayer-dialog button[data-action="cancel"]').click(function() {
    hideDialog();
  });

  $('#ymplayer-dialog button[data-action="submit"]').click(function() {
    var value = {},
      opt = [],
      textarea = $('#text');
    if (!document.getElementById('ymplayer-dialog-form').checkValidity()) {
      alert('Some fields are empty.');
      return false;
    }
    $('#ymplayer-dialog-form input[data-name]').each(function(index, elem) {
      var $elem = $(elem),
        attr = $elem.attr('data-name');
      value[attr] = $elem.val().replace(/(http.?):\/\//g, '[$1]:\/\/');
    });
    var matches = textarea.val().match(/{(YMPlayer)}(.*?){\/YMPlayer}/i);
    if (matches && matches[2]) {
      opt = matches[2];
      opt = JSON.parse(opt);
      opt.push(value);
      textarea.val(textarea.val().replace(matches[2], JSON.stringify(opt)));
    } else {
      opt.push(value);
      grin('{YMPlayer}' + JSON.stringify(opt) + '{/YMPlayer}');
    }

    hideDialog();
  });

  function showDialog() {
    $('#ymplayer-dialog-bg').show();
    $('#ymplayer-dialog').show();
  }

  function hideDialog() {
    $('#ymplayer-dialog-form input').val('');
    $('#ymplayer-dialog-bg').hide();
    $('#ymplayer-dialog').hide();
  }

  function grin(tag) {
    var myField;
    if (document.getElementById('text') && document.getElementById('text').type == 'textarea') {
      myField = document.getElementById('text');
    } else {
      return false;
    }
    if (document.selection) {
      myField.focus();
      sel = document.selection.createRange();
      sel.text = tag;
      myField.focus();
    } else if (myField.selectionStart || myField.selectionStart == '0') {
      var startPos = myField.selectionStart;
      var endPos = myField.selectionEnd;
      var cursorPos = startPos;
      myField.value = myField.value.substring(0, startPos)
      + tag
      + myField.value.substring(endPos, myField.value.length);
      cursorPos += tag.length;
      myField.focus();
      myField.selectionStart = cursorPos;
      myField.selectionEnd = cursorPos;
    } else {
      myField.value += tag;
      myField.focus();
    }
  }

  $('body').on('keydown', function(a) {
    if (a.altKey && a.keyCode == "77") {
      $('#wmd-music-button').click();
    }
  });
});
