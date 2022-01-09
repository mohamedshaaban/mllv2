//Share Order Info Via Whatsapp

window.Clipboard = (function(window, document, navigator) {
    var textArea,
        copy;

    function isOS() {
        return navigator.userAgent.match(/ipad|iphone/i);
    }

    function createTextArea(text) {
        textArea = document.createElement('textArea');
        textArea.value = text;
        document.body.appendChild(textArea);
    }

    function selectText() {
        var range,
            selection;

        if (isOS()) {
            range = document.createRange();
            range.selectNodeContents(textArea);
            selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            textArea.setSelectionRange(0, 999999);
        } else {
            textArea.select();
        }
    }

    function copyToClipboard() {

        document.execCommand('copy');
        document.body.removeChild(textArea);
    }

    copy = function(text) {
        createTextArea(text);
        selectText();
        copyToClipboard();
    };

    return {
        copy: copy
    };
})(window, document, navigator);

function shareOrder(id)
{
    $.ajax
    ({
        type: "GET",
        //url: "#",
        url: "/share/order/"+id,
        success: function(data)
        {
            $('#texttoshare').html(data);
            window.open(data, '_blank');
        }
    });
}
//Copy Order Info
function copyOrder(id)
{
    $.ajax
    ({
        type: "GET",
        dataType: 'html',
        url: "/copy/order/"+id,
        success: function(data)
        {
            $('#texttoshare').html(data);
            $('#myInput').val(data);
            setTimeout(() => {  copyToClipboard('#texttoshare'); }, 1000);


         }
    });
}
function copyToClipboard(element) {

    let textarea;
    let result;
    string = $('#myInput').val();

    try {
        textarea = document.createElement('textarea');
        textarea.setAttribute('readonly', true);
        textarea.setAttribute('contenteditable', true);
        textarea.style.position = 'fixed'; // prevent scroll from jumping to the bottom when focus is set.
        textarea.value = string;

        document.body.appendChild(textarea);

        textarea.focus();
        textarea.select();

        const range = document.createRange();
        range.selectNodeContents(textarea);

        const sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);

        textarea.setSelectionRange(0, textarea.value.length);
        result = document.execCommand('copy');
    } catch (err) {
        console.error(err);
        result = null;
    } finally {
        document.body.removeChild(textarea);
    }

    // manual copy fallback using prompt
    // if (!result) {
        const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
        const copyHotkey = isMac ? '⌘C' : 'CTRL+C';
        result = prompt(`Press ${copyHotkey}`, string); // eslint-disable-line no-alert
    //     if (!result) {
    //         return false;
    //     }
    // }
    alert('Order Info Copied');
}


function copyURI(evt) {
    evt.preventDefault();
    navigator.clipboard.writeText(evt.target.getAttribute('href')).then(() => {
        /* clipboard successfully set */
    }, () => {
        /* clipboard write failed */
    });
}
$('.modeltext-class').parent().fadeOut();

$('.not_exits_make-class').change(function() {
    if(this.checked) {
         $('.modeltext-class').parent().fadeIn();
        $('.modelselect-class').parent().fadeOut();
    }
    else
    {
         $('.modeltext-class').parent().fadeOut();
        $('.modelselect-class').parent().fadeIn();
    }
});

$( ".paymenttype-class" ).change(function() {
     if ($(this).val() == '2') {
          $('.paidpayment-class').parent().fadeOut();
    }
    else
    {

        $('.paidpayment-class').parent().fadeIn();
    }
});
