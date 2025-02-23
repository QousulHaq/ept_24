import { Howl, Howler } from 'howler'

class InternalPlayer {
	constructor() {
		this.data = []
	}

	pause () {
		for (let instance of this.data) {
			instance.pause()
		}
	}

	play () {
		return new Promise((resolve, reject) => {
			// noinspection JSValidateTypes
			const onEnd = () => this.data.shift() && this.data.length ? this.data[0].play() : resolve()
			for (let instance of this.data) {
				instance.on('end', onEnd)
			}

			if (this.data.length > 0) {
				this.data[0].play()
			} else {
				reject()
			}
		})
	}
}

export const howler = {
	instances: {},
	/**
	 * @param identifier
	 * @param {Array.<{url: String, format: String}>} sources
	 * @return {Promise<InternalPlayer>|InternalPlayer}
	 */
	load (sources = [], identifier = null) {
		if (identifier == null) {
			identifier = Date.now()
		}

		if (this.instances[identifier]) {
			return this.instances[identifier]
		}

		return new Promise((resolve, reject) => {
			let loaded = 0
			let generics = new InternalPlayer()
			const onLoad = () => {
				if (++loaded === sources.length) {
					resolve(this.instances[identifier] = generics, identifier)
				}
			}
			for (let source of sources) {
				generics.data.push(new Howl({
					src: [source.url],
					format: [source.format],
					autoplay: false,
					preload: true,
					onload: onLoad,
					onloaderror: (e) => reject(e)
				}))
			}
		})
	},
	pauseAll () {
		for (const key in this.instances) {
			if (this.instances.hasOwnProperty(key)) {
				this.instances[key].pause()
			}
		}
	},
	getInstance (identifier) {
		return this.instances[identifier] ?? null
	}
}