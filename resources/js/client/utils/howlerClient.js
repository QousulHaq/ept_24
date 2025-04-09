// import { Howl } from "howler";

// class HowlerClient {
// 	constructor() {
// 		this.instances = {}; // Menyimpan semua instance audio
// 	}

// 	load(sources = [], identifier = null) {
// 		return new Promise((resolve, reject) => {
// 			if (!identifier) identifier = Date.now();

// 			if (this.instances[identifier]) {
// 				resolve(this.instances[identifier]);
// 				return;
// 			}

// 			let loadedCount = 0;
// 			let player = {
// 				data: sources.map((source) => {
// 					return new Howl({
// 						src: [source.url],
// 						format: [source.format],
// 						autoplay: false,
// 						preload: true,
// 						onload: () => {
// 							loadedCount++;
// 							if (loadedCount === sources.length) {
// 								resolve(this.instances[identifier] = player);
// 							}
// 						},
// 						onloaderror: (id, error) => {
// 							console.error("Howler Load Error:", error);
// 							reject(error);
// 						},
// 					});
// 				}),
// 				play() {
// 					return new Promise((resolve, reject) => {
// 						if (this.data.length > 0) {
// 							const sound = this.data[0];

// 							// Hapus listener sebelumnya jika sudah ada
// 							sound.off("end");

// 							// Pasang listener baru hanya sekali
// 							sound.once("play", () => {
// 								console.log("Audio started playing...");
// 							});

// 							sound.once("end", () => {
// 								console.log("Audio finished playing.");
// 								resolve();
// 							});

// 							// Tangani error jika ada
// 							sound.once("playerror", (id, error) => {
// 								console.error("Error playing audio:", error);
// 								reject(error);
// 							});

// 							sound.play();
// 						} else {
// 							reject(new Error("No audio data available to play"));
// 						}
// 					});
// 				},
// 				pause() {
// 					this.data.forEach((instance) => instance.pause());
// 				},
// 			};

// 			this.instances[identifier] = player;
// 		});
// 	}

// 	getInstance(identifier) {
// 		return this.instances[identifier] ?? null;
// 	}

// 	pauseAll() {
// 		Object.values(this.instances).forEach((instance) => instance.pause());
// 	}
// }

// export const howler = new HowlerClient();

// src/utils/howler.js
import { Howl } from 'howler'

class InternalPlayer {
  constructor() {
    this.data = []
    this.hasPlayed = false // Tambahkan flag untuk melacak apakah audio sudah diputar
  }

  pause() {
    for (const instance of this.data) {
      instance.pause()
    }
  }

  play() {
    // Jika tidak ada data atau sudah diputar, kembalikan Promise yang langsung resolve
    if (this.data.length === 0 || this.hasPlayed) {
      console.log('No audio to play or already played')
      return Promise.resolve()
    }

    return new Promise((resolve, reject) => {
      const onEnd = () => {
        this.data.shift()
        if (this.data.length > 0) {
          this.data[0].play()
        } else {
          this.hasPlayed = true // Tandai bahwa semua audio sudah diputar
          resolve()
        }
      }

      for (const instance of this.data) {
        instance.on('end', onEnd)
      }

      if (this.data.length > 0) {
        this.data[0].play()
      } else {
        reject(new Error('No audio to play'))
      }
    })
  }

  // Tambahkan method untuk reset status
  reset() {
    this.hasPlayed = false
  }
}

const howler = {
  instances: {},

  /**
   * Load audio tracks under a unique identifier
   * @param {Array<{url: string, format: string}>} sources
   * @param {string|number|null} identifier
   * @returns {Promise<InternalPlayer>}
   */
  load(sources = [], identifier = null) {
    if (identifier == null) {
      identifier = Date.now()
    }

    if (this.instances[identifier]) {
      return Promise.resolve(this.instances[identifier])
    }

    return new Promise((resolve, reject) => {
      let loaded = 0
      const generics = new InternalPlayer()

      const onLoad = () => {
        if (++loaded === sources.length) {
          this.instances[identifier] = generics
          resolve(generics)
        }
      }

      for (const source of sources) {
        generics.data.push(
          new Howl({
            src: [source.url],
            format: [source.format],
            autoplay: false,
            preload: true,
            onload: onLoad,
            onloaderror: (id, error) => reject(error),
          })
        )
      }
    })
  },

  pauseAll() {
    for (const key in this.instances) {
      if (Object.prototype.hasOwnProperty.call(this.instances, key)) {
        this.instances[key].pause()
      }
    }
  },

  getInstance(identifier) {
    return this.instances[identifier] ?? null
  },
}

export default howler
