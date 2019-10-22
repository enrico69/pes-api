$(document).ready(function() {
    $('#matchSavingButton').click(function () {
        if (confirm("Etes-vous sûr de vouloir terminer le match?") == true) {
            handleSubmission();
        }
    });
});

function pleaseWait(wait) {
    if (wait) {
        $('#spinner').show();
        $('#screen_actions').hide();
    } else {
        $('#spinner').hide();
        $('#screen_actions').show();
    }
}

function handleSubmission() {
    var payload = {};
    payload['matchData'] = matchData;
    console.log(matchData);
    console.log('Submitting matchData...');

    pleaseWait(true);

    $.ajax({
        type:"POST",
        data: payload,
        url: updateSubmitURL,
        success: function(data) {
            if(data['status'] == 'success') {
                alert('Le match a été enregistré avec succès');
                window.location.replace(homeURL)
            } else {
                alert("Une erreur s'est produite :( ");
                pleaseWait(false);
            }
        },
        error: function() {
            alert('Impossible de soumettre la mise à jour du match!');
            pleaseWait(false);
        },
    });
}