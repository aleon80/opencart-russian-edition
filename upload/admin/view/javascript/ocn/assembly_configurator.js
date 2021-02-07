$(document).ready(function () {
  let checkedModifications = [];

  /**
   * Refresh
   */
  // Action
  $('#tab-modifications').on('click', '#form-modifications button[name=refresh]', refreshModifications);
  // Functions
  function refreshModifications() {
    checkedModifications = [];
    $('#form-modifications-table').html('');
    lockElements($('#form-modifications button[name=refresh]'), 'fa-refresh');

    $("#form-modifications-table").load(
      $('#form-modifications').data('refresh'),
      function (response, status, xhr) {
        if (status === "error") {
          showAlert($('#main'), 'danger', 'Error!', msg + xhr.status + " " + xhr.statusText);
        }
        unLockElements($('#form-modifications button[name=refresh]'), 'fa-refresh');
      });
  }

  /**
   * Selected modifications
   */
  $('#tab-modifications').on('click', '#form-modifications-select-all', function () {
    checkedModifications = [];
    $('#form-modifications input[name*="modifications"]:not(:disabled)').prop('checked', this.checked);
    if (this.checked) {
      $('#form-modifications input[name*="modifications"]:checked').each(function() {
        checkedModifications.push(this.value);
      });
    }
    checkModificationsSelectedStatus();
    checkModificationsButtonsStatus();
  })
  $('#tab-modifications').on('change', 'input[name*="modifications"]', function () {
    if (this.checked) {
      checkedModifications.push(this.value);
    } else {
      checkedModifications.splice(checkedModifications.indexOf(this.value), 1);
    }
    checkModificationsSelectedStatus();
    checkModificationsButtonsStatus();
  })
  function checkModificationsSelectedStatus() {
    let status = false;
    if (checkedModifications.length == $('#form-modifications input[name*="modifications"]:not(:disabled)').length) {
      status = true;
    }
    $('#form-modifications #form-modifications-select-all').prop('checked', status);
  }
  function checkModificationsButtonsStatus() {
    let status = true;
    if (checkedModifications.length > 0) {
      status = false;
    }
    $('#form-modifications #form-modifications-button-install').attr('disabled', status);
  }

  /**
   * Install or/and Update
   */
  // Action
  $('#tab-modifications').on('click', 'button[name=install]', installModifications);
  // Function
  function installModifications() {
    lockElements($('#form-modifications button[name=install]'), 'fa-plus');
    $.ajax({
      type: 'post',
      url: $('#form-modifications').attr('action'),
      data: {modifications: checkedModifications},
      dataType: 'json',
      success: function (response) {
        let color = 'danger';
        let status = 'warning';
        let message = 'error';

        if (response.status) {
          color = response.color;
          status = response.text_status;
          message = response.text_message;
        }

        showAlert($('#main'), color, status, message);
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      },
      complete: function () {
        refreshModifications();
        unLockElements($('#form-modifications button[name=install]'), 'fa-plus');
      }
    });
  }

  // Alerts
  function showAlert(block, color, status, text) {
    block.find('> .alert').hide('slow');
    const alertBlock = '<div class="alert alert-' + color + ' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>' + status + '</strong> ' + text + '</div>';
    block.prepend(alertBlock);
  }
  function clearAlerts(block = false) {
    if (block) {
      block.find('> .alert').hide('slow');
    } else {
      $('.alert').hide('slow');
    }
  }

  function lockElements(lockElement, faClass) {
    lockElement.find('i.fa').removeClass(faClass).addClass('fa-spinner fa-pulse');
    $('#form-modifications button[name=refresh]').prop('disabled', true);
    $('#form-modifications button[name=install]').prop('disabled', true);
  }
  function unLockElements(unLockElement, faClass) {
    unLockElement.find('i.fa').removeClass('fa-spinner fa-pulse').addClass(faClass);
    $('#form-modifications button[name=refresh]').prop('disabled', false);
  }
});
