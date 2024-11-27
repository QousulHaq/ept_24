import LineCount from "../components/extra/LineCount";
import AudioForm from "../components/extra/AudioForm";

export default {
	data: () => ({
		line_count_context: undefined
	}),
	components: { LineCount, AudioForm },
	computed: {
		extra: function () {
			return _.fromPairs(_.map(_.get(this.config, 'extra', []), function (value) {
				let split = value.split(':');

				return (split.length === 1) ? [split, true] : split
			}))
		},
		contentStyle: function () {
			const width = this.getExtra('width');
			return {
				... width ? { width } : {},
				... this.getExtra('no_content') ? { display: 'none' } : {}
			}
		},
	},
	watch: {
		'form.content': function () {
			this.lineCount();
			this.answerFromContent()
		}
	},
	methods: {
		getExtra(name) {
			return this.extra[name] ?? false
		},
		lineCount: _.debounce(function () {
			const line_count = this.getExtra('line_count');
			if (line_count) {
				const context = {
					items: []
				};

				let i = 1;
				for (const el of this.$refs['content'].$el.children.item(0)?.children) {
					context.items.push({
						height: el.clientHeight + 'px',
						color: i % Number(line_count) === 0 ? '#000' : '#9d9b9b',
					});
					i++;
				}

				this.line_count_context = context
			}
		}),
		answerFromContent: _.debounce(function () {
			const answers_from_content = this.getExtra('answers_from_content');
			if (answers_from_content) {
				const oldAnswers = [...this.form.answers], newAnswers = [];
				this.deleteAllAnswers();
				for (let result of this.form.content.matchAll(/<u>(.*?)<\/u>/g)) {
					newAnswers.push(oldAnswers.find(a => a.content.trim() === result[1].trim()) ?? {
						content: result[1].trim(),
						correct_answer: false
					})
				}

				let i = 0;
				for (let answer of newAnswers) {
					this.addAnswer({
						...answer,
						...{
							order: i
						}
					});
					i++
				}
			}
		}),
	},
	mounted () {
		this.$nextTick(function () {
			this.answerFromContent()
		})
	}
}
