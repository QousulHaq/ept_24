import _ from 'lodash'
import { mapState } from 'vuex'

// warning : don't use nested object. it will referenced instead, no matter what
const DEFAULT_DATA = {
	line_count_context: null,
	audioContext_played: false,
	audioContext_instance: null
}

export default {
	data: () => _.cloneDeep({ ...DEFAULT_DATA }),
	computed: {
		...mapState('auth', {
			isConnected: state => state.connection_state === 'connected'
		}),
		extra: function () {
			return _.fromPairs(_.map(
				_.merge(_.get(this.item, 'config.extra', []), _.get(this.item, 'config.sub-item.extra')), function (value) {
					let split = value.split(':');
					return (split.length === 1) ? [split, true] : split
				}))
		},
		contentStyle: function () {
			const width = this.getExtra('width');
			return {
				... width ? { width } : {},
				... this.getExtra('no_content') ? { display: 'none' } : {},
				... this.getExtra('line_count') ? { transform: 'translateX(26px)' } : {}
			}
		},
	},
	watch: {
		isConnected: function (value) {
			if (! value && this.audioContext_played) {
				this.audioContext_instance.pause()
			} else if (this.audioContext_played) {
				this.audioContext_instance.play()
			}
		}
	},
	methods: {
		resetPluginData () {
			for (const key in DEFAULT_DATA) {
				if (this[key] !== null) this[key] = DEFAULT_DATA[key]
			}
		},
		bootPlugins () {
			this.lineCount()
			this.audio()
		},
		getExtra (name) {
			return this.extra[name] ?? false
		},
		lineCount: _.debounce(function () {
			const line_count = this.getExtra('line_count');
			const refContent = this.$refs['content'];

			if (line_count && refContent) {
				const context = {
					items: []
				};

				let i = 1;
				for (const el of refContent.$el.children.item(0)?.children) {
					context.items.push({
						height: el.clientHeight + 'px',
						color: i % Number(line_count) === 0 ? '#000' : '#9d9b9b',
					})
					i++;
				}

				this.line_count_context = context
			} else {
				this.line_count_context = null
			}
		}),
		audio (readOf = 'item') {
			const hasAudio = this.getExtra('audio')
			if (hasAudio) {
				this.$emit('onCountdownFreezeChange', true)
				this.audioContext_instance = this.howler.getInstance(this[readOf].id)
				if (! this.audioContext_instance) {
					if (process.env.NODE_ENV !== 'production')
						console.log(this[readOf])

					this.$emit('onCountdownFreezeChange', false)

					return console.warn(`instance of howler with id ${this[readOf].id} not found!.`)
				}

				this.howler.pauseAll()

				if (this.isConnected) {
					let halfRemainingTime = Math.floor(parseInt(this.item['remaining_time']) / 2)
					if (this.getExtra('time_audio_split') && halfRemainingTime > 3) {
						// pause audio till half of remaining time
						this.$emit('onCountdownFreezeChange', false)
						this.audioContext_played = false

						setTimeout(() => {
							// reach half of remaining time then play audio
							this.$emit('onCountdownFreezeChange', true)
							this.audioContext_played = true
							this.audioContext_instance.play().then(() => {
								this.$emit('onCountdownFreezeChange', false)
								this.audioContext_played = false
							})
						}, halfRemainingTime * 1000)
					} else {
						this.audioContext_played = true
						this.audioContext_instance.play().then(() => {
							this.$emit('onCountdownFreezeChange', false)
							this.audioContext_played = false
						})
					}
				}
			}
		}
	}
}
