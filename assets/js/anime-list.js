document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('fetch-anime').addEventListener('click', function () {
        fetch(mg_anime_list_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'fetch_anime_data'
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Iteramos sobre los animes y los mostramos
                    let resultDiv = document.getElementById('anime-result');
                    resultDiv.innerHTML = ''; // Limpiamos el contenido anterior

                    data.data.forEach(anime => {
                        let animeHtml = `
                        <div>
                            <h2>${anime.title}</h2>
                            <p><strong>ID:</strong> ${anime.id}</p>
                            <p><strong>Synopsis:</strong> ${anime.synopsis}</p>
                            <p><strong>Poster:</strong> <img src="${anime.posterImage}" alt="${anime.title}"></p>
                            <p><strong>Cover Image:</strong> <img src="${anime.coverImage}" alt="${anime.title}"></p>
                            <p><strong>Categories:</strong> ${anime.categories.join(', ')}</p>
                        </div>
                        <hr>
                    `;
                        resultDiv.innerHTML += animeHtml;
                    });
                } else {
                    document.getElementById('anime-result').innerHTML = '<p>Error fetching anime data</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('anime-result').innerHTML = '<p>Error fetching anime data</p>';
            });
    });
});
