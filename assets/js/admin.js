document.addEventListener('DOMContentLoaded', () => {
    const team1 = document.querySelector('#team1_id');
    const team2 = document.querySelector('#team2_id');

    const syncTeams = () => {
        if (!team1 || !team2) return;
        if (team1.value && team1.value === team2.value) {
            team2.setCustomValidity('Les deux équipes doivent être différentes.');
        } else {
            team2.setCustomValidity('');
        }
    };

    if (team1 && team2) {
        team1.addEventListener('change', syncTeams);
        team2.addEventListener('change', syncTeams);
    }
});
