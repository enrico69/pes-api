/**
 * NOTE FOR LATER. In the next iteration, replace all this temporary and HORRIBLE code by proper POO with ES6.
 * NEED also to replace callback hell with with async awaits.
 * In this code, we see the power of Agility and Lean :)
 */
var gamers = {};
var teams = {};
var teamsPageCount = 2;
var winnerGamerId;

$(document).ready(function() {
    $('#tournamentCreationButton').click(function () {
        handleSubmission();
    });

    teams[0] = 'AutoSelection';

    $.ajax ({
        type: 'GET',
        url: gamersEndPoint,
        contentType: "application/json",
        dataType: "json",
        success: function(data) {
            $.each( data, function( key, val ) {
                gamers[val.id] = val.name;
            });
            getTeams(0);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Impossible de récuperer la liste des joueurs.');
        } ,
        timeout: 20000
    });
});

function getTeams(count) {
    count++;

    $.ajax ({
        type: 'GET',
        url: teamsEndPoint + count,
        contentType: "application/json",
        dataType: "json",
        success: function(data) {
            $.each( data, function( key, val ) {
                teams[val.id] = val.name;
            });

            if(count <= teamsPageCount) {
                getTeams(count);
            }

            if (count === teamsPageCount) {
                addSelectGamer();
                $('#spinner').hide();
                $('#gamerForm').show();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Impossible de récuperer la liste des joueurs.');
        } ,
        timeout: 20000
    });
}

function addSelectGamer() {
    var teamSelector = '';
    $.each( teams, function( id, name ) {
        teamSelector += '<option value="' + id + '">' + name + '</option>';
    });
    teamSelector += '</select>';

    for (var count = 1; count <= 4; count++) {
        var newElement = '<select style="margin-left: 10px; border:1px solid #000000;" name="gamer_' + count + '"  id="gamer_' + count + '">';
        $.each( gamers, function( id, name ) {
            newElement += '<option value="' + id + '">' + name + '</option>';
        });
        newElement += '</select> ';
        $('#select-Gamers').append(newElement);

        var teamSelectorBegin = '<select style="margin-left: 10px; border:1px solid #000000;" name="team_' + count + '"  id="team_' + count + '">';
        var currentTeamSelector = teamSelectorBegin + teamSelector;
        $('#select-Gamers').append(currentTeamSelector);
        var checkboxWinner = '<input style="margin-left: 10px; border:1px solid #000000;" onclick="selectWinner(this)" type="checkbox" name="winner[]" id="winner_' + count + '" value="' + count + '"> Tenant du titre';
        $('#select-Gamers').append(checkboxWinner);
        $('#select-Gamers').append('<br/><br/><br/>');
    }
}

function selectWinner(element) {
    var id = element.id;
    var isChecked = false;
    winnerGamerId = null;
    if($('#' + id).prop( "checked")) {
        isChecked = true;
    }

    $('#winner_1').prop( "checked", false );
    $('#winner_2').prop( "checked", false );
    $('#winner_3').prop( "checked", false );
    $('#winner_4').prop( "checked", false );

    if (isChecked) {
        $('#' + id).prop( "checked", true );
        winnerGamerId = id.substring(7);
    }
}

function handleSubmission() {
    console.log('Generating payload...');
    var payload = {};

    payload[tournamentTypeKey] = tournamentType;
    payload['withCup'] = true; // @TODO When enabling the choice, use constant for the key like for the other fields of the payload
    if (winnerGamerId) {
        payload[winnerIdField] = $('#gamer_' + winnerGamerId).val();
    }

    var teams = {};
    teams[$('#team_1').val()] = $('#team_1').val();
    teams[$('#team_2').val()] = $('#team_2').val();
    teams[$('#team_3').val()] = $('#team_3').val();
    teams[$('#team_4').val()] = $('#team_4').val();


    var associations = {};
    associations[$('#gamer_1').val()] = $('#team_1').val();
    associations[$('#gamer_2').val()] = $('#team_2').val();
    associations[$('#gamer_3').val()] = $('#team_3').val();
    associations[$('#gamer_4').val()] = $('#team_4').val();
    payload[associationsField] = associations;
    payload[forcedIdField] = $('#' + forcedIdField).val();

    console.log("Validation...");
    console.log(associations);
    console.log(teams);
    if (Object.keys(associations).length != 4 || Object.keys(teams).length != 4) {
        alert('Il faut 4 joueurs différents et 4 équipes différentes (dont une auto-sélection au maximum)!');

        return;
    }

    console.log('Submitting payload...');

    $('#spinner').show();
    $('#gamerForm').hide();
    $.ajax({
        type:"POST",
        data: payload,
        dataType: "html",
        url: creationSubmitURL,
        success: function(data) {
            if(data == '{"status":"success"}') {
                alert('Le tournoi a été créé avec succès');
                window.location.replace(homeURL)
            } else {
                alert("Une erreur s'est produite :( ");
                $('#spinner').hide();
                $('#gamerForm').show();
            }
        },
        error: function() {
            alert('Impossible de soumettre la création du tournoi!');
            $('#spinner').hide();
            $('#gamerForm').show();
        },
    });
}
