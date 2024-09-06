document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('fetch-anime').addEventListener('click', function () {
        fetch(mg_anime_list_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-WP-Nonce': mg_anime_list_ajax.nonce
            },
            body: new URLSearchParams({
                action: 'fetch_anime_data'
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('anime-result').innerText = 'Data fetched and posts created successfully.';

                    console.log(data);
                } else {
                    document.getElementById('anime-result').innerText = 'Error fetching data.';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('anime-result').innerHTML = '<p>Error fetching anime data</p>';
            });
    });
});
