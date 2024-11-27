<template>
  <div class="card">
    <div class="card-header justify-content-between">
      <h4>{{title}}</h4>
      <button type="button" class="close">
        <span aria-hidden="true" @click="$emit('onClose')">&times;</span>
      </button>
    </div>
    <div class="card-body">
      <line-count v-if="this.getExtra('line_count')" :leap="this.getExtra('line_count')" :context="line_count_context"/>
      <audio-form v-if="this.getExtra('audio')" v-model="form.attachment"/>
      <editor-menu-bar :editor="editor" v-slot="{ commands, isActive }" :style="contentStyle">
        <div class="menubar">
          <button class="menubar__button" :class="{ 'is-active': isActive.heading({ level: 1 }) }"
                  @click="commands.heading({ level: 1 })">
            H1
          </button>
          <button class="menubar__button" :class="{ 'is-active': isActive.heading({ level: 2 }) }"
                  @click="commands.heading({ level: 2 })">
            H2
          </button>
          <button class="menubar__button" :class="{ 'is-active': isActive.heading({ level: 3 }) }"
                  @click="commands.heading({ level: 3 })">
            H3
          </button>
          <button class="menubar__button" :class="{ 'is-active': isActive.heading({ level: 4 }) }"
                  @click="commands.heading({ level: 4 })">
            H4
          </button>
          <button class="menubar__button" :class="{ 'is-active': isActive.heading({ level: 5 }) }"
                  @click="commands.heading({ level: 5 })">
            H5
          </button>
          <button class="menubar__button" :class="{ 'is-active': isActive.bold() }" @click="commands.bold">
            <i class="fas fa-bold"/>
          </button>
          <button class="menubar__button" :class="{ 'is-active': isActive.italic() }" @click="commands.italic">
            <i class="fas fa-italic"/>
          </button>
          <button class="menubar__button" :class="{ 'is-active': isActive.strike() }" @click="commands.strike">
            <i class="fas fa-strikethrough"/>
          </button>
          <button class="menubar__button" :class="{ 'is-active': isActive.underline() }" @click="commands.underline">
            <i class="fas fa-underline"/>
          </button>
          <button class="menubar__button" @click="commands.undo">
            <i class="fas fa-undo"/>
          </button>
          <button class="menubar__button" @click="commands.redo">
            <i class="fas fa-redo"/>
          </button>
        </div>
      </editor-menu-bar>
      <editor-content :class="{'editor__content': true, 'alphabet_counter': getExtra('alphabet_counter_underline')}" :editor="editor" v-model="form.content" ref="content" :style="contentStyle"/>
    </div>
    <div class="card-footer">
      <div class="row">
        <div class="col-md-12">
          <section class="section">
            <div class="section-body">
              <h4 class="section-title">Answer</h4>
              <p class="section-lead">#</p>
              <single-answer v-for="(_, i) in form.answers" :key="i" :name="$vnode.key"
                             v-model="form.answers[i].content"
                             :checked="form.answers[i].correct_answer"
                             @selected="selectedAnswerChanged"
                             :closable="!getExtra('answers_from_content')"
                             :editable="!getExtra('answers_from_content')"
                             @closed="deleteAnswer"/>
            </div>
          </section>
        </div>
        <div class="col-md-12" v-if="!getExtra('answers_from_content')">
          <button class="btn btn-sm btn-block" @click="addAnswer()">
            <i class="fa fa-plus"></i> Add Answer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

  import {ITEM_TYPE_MULTI_CHOICE_SINGLE} from "../../const"
  import {Editor, EditorContent, EditorMenuBar} from 'tiptap'
  import {Bold, History, Italic, Strike, Underline, HorizontalRule, Heading} from 'tiptap-extensions'
  import SingleAnswer from "../answer/SingleAnswer"
  import HasExtraConfigMixin from "../../mixins/HasExtraConfigMixin";

  export default {
    name: "QuizBuilderMultiChoiceSingle",
    mixins: [HasExtraConfigMixin],
    props: {
      title: {
        type: String,
        default: null
      },
      config: {
        type: Object,
        default: function () {
          return {}
        }
      },
      value: {
        type: Object
      }
    },
    data: () => ({
      form: {
        type: ITEM_TYPE_MULTI_CHOICE_SINGLE,
        attachment: undefined,
        answer_order_random: true,
        content: '<p></p>',
        answers: []
      },
      editor: null,
    }),
    watch: {
      form: {
        handler: function (newValue) {
          this.$emit('input', newValue)
        },
        deep: true
      },
    },
    methods: {
      addAnswer (values = { content: '', correct_answer: false }, force = false) {
        if (!this.form.answers.some(a => a.content === values.content) || force) {
          this.form.answers.push({
            order: values?.order ?? this.form.answers.length,
            ...values
          })
        }
      },
      selectedAnswerChanged (index) {
        this.form.answers.forEach((value, i) => {
          value.correct_answer = Boolean( i === parseInt(index))
        })
      },
      deleteAnswer (index) {
        this.selectedAnswerChanged(-1);
        this.form.answers.splice(index, 1);

        let i = 0;
        for (let answer of this.form.answers) {
          answer.order = i;
          i++;
        }
      },
      deleteAllAnswers () {
        this.form.answers = []
      }
    },
    components: { EditorMenuBar, EditorContent, SingleAnswer },
    created () {
      this.form = Object.assign({}, this.form, this.value, _.pick(this.config, ['answer_order_random', 'duration']))
    },
    mounted () {
      this.editor = new Editor({
        extensions: [
          new Heading({ levels: [1, 2, 3, 4, 5] }),
          new Bold(),
          new Italic(),
          new Strike(),
          new Underline(),
          new History(),
          new HorizontalRule(),
        ],
        onUpdate: ({ getHTML }) => {
          this.form.content = getHTML()
        }
      });

      this.editor.setContent(this.form.content?.replaceAll(' ', '&nbsp;'))

      this.$nextTick(() => {
        if (this.value.answers.length === 0)
          for (let i = 0; i < _.get(this.config, 'answer_count', 0); i++)
            this.addAnswer();
      })
    }
  }
</script>

<style scoped>

</style>

<style>
.alphabet_counter {
  counter-reset: uwu;
}

.alphabet_counter u:before {
  display: inline-block;
  counter-increment: uwu;
  content: "(" counter(uwu, upper-alpha) ")";
  text-decoration: none;
  position: relative;
  padding-bottom: 15px;
}
</style>
