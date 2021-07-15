function runNowPlaying() {
	const songPreRender = document.querySelector('.song-archive-prerender');
	if (songPreRender) {
		const callsign = songPreRender.dataset['callsign'];
		const endpoint = songPreRender.dataset['endpoint'];
		const description = songPreRender.dataset['description'];
		const state = {
			loading: true,
			days: [],
			songCollectionByDay: [],
			description: description,
			callsign: callsign,
		}
		render(songPreRender, state);

		window.jQuery.get(endpoint)
			.then(result => {
				state.days = [
					...new Set(
						result.map(song =>
							dayjs.unix(song.timestamp).format('MMMM D, YYYY'),
						),
					),
				];

				const songCollectionByDay = new Map();
				state.days.map(day => songCollectionByDay.set(day, []));

				result.map(song =>
					songCollectionByDay
						.get(dayjs.unix(song.timestamp).format('MMMM D, YYYY'))
						.push(song),
				);

				state.loading = false;
				state.songCollectionByDay = songCollectionByDay;

				render(songPreRender, state);
			})
			.fail(() => {
				console.log('unable to load ' + endpoint)
				state.loading = false;
				state.songCollectionByDay = [];
				state.days = [];
				render(songPreRender, state);
			});
	}
}

function render(songPreRender, state) {
	songPreRender.innerHTML = '';

	const container = document.createElement('div');
	container.className = 'song-archive';
	songPreRender.appendChild(container);

	const nowPlayingHeader = document.createElement('h3');
	nowPlayingHeader.innerText = 'Recently Played Songs on ' + state.description;

	container.appendChild(nowPlayingHeader);

	if (state.loading) {
		const loadingMessage = document.createElement('p');
		loadingMessage.innerText = 'Loading ...';
		container.appendChild(loadingMessage);
	} else {
		const resultDiv = document.createElement('div');
		for (const day of state.days) {
			const dayDiv = document.createElement('div');
			const dayLabel = document.createElement('h4');
			const songList = document.createElement('ul');

			dayLabel.innerText = day;
			dayDiv.appendChild(dayLabel);


			for (const song of state.songCollectionByDay.get(day)) {
				const songItem = document.createElement('li');
				const songTime = document.createElement('span');
				const songTitle = document.createElement('span');
				const songArtist = document.createElement('span')

				songTime.className = 'song-time';
				songTime.innerText = dayjs.unix(song.timestamp).format('h:mm A');

				songTitle.className = 'song-title';
				songTitle.innerText = song.title;

				songArtist.className = 'song-artist';
				songArtist.innerText = song.artist;

				songItem.appendChild(songTime);
				songItem.appendChild(document.createTextNode(' \u00A0'));
				songItem.appendChild(songTitle);
				songItem.appendChild(document.createTextNode('\u2014'));
				songItem.appendChild(songArtist);


				songList.appendChild(songItem)
			}

			dayDiv.appendChild(songList);

			resultDiv.appendChild(dayDiv);
		}
		container.appendChild(resultDiv);
	}
}

runNowPlaying();
