<template>
  <div>
    <div class="card">
      <div class="card-header">
        <h4>{{title}}</h4>
      </div>
      <div class="card-body table-responsive">
        <audio-form v-if="this.getExtra('audio')" v-model="form.attachment"/>
        <editor-menu-bar :editor="editor" v-slot="{ commands, isActive }" :style="contentStyle">
          <div class="menubar">
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
            <button class="menubar__button" :class="{ 'is-active': isActive.paragraph() }" @click="commands.paragraph">
              <i class="fas fa-paragraph" />
            </button>
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
            <button class="menubar__button" :class="{ 'is-active': isActive.bullet_list() }" @click="commands.bullet_list">
              <i class="fas fa-list-ul"/>
            </button>
            <button class="menubar__button" :class="{ 'is-active': isActive.ordered_list() }" @click="commands.ordered_list">
              <i class="fas fa-list-ol"/>
            </button>
            <button class="menubar__button" :class="{ 'is-active': isActive.blockquote() }" @click="commands.blockquote">
              <i class="fas fa-quote-left"/>
            </button>
            <button class="menubar__button" @click="commands.horizontal_rule">
              <i class="fas fa-ruler-horizontal"/>
            </button>
            <button class="menubar__button" @click="commands.undo">
              <i class="fas fa-undo"/>
            </button>
            <button class="menubar__button" @click="commands.redo">
              <i class="fas fa-redo"/>
            </button>
          </div>
        </editor-menu-bar>
        <line-count v-if="this.getExtra('line_count')" :leap="this.getExtra('line_count')" :context="line_count_context"/>
        <editor-content class="editor__content" :editor="editor" v-model="form.content" :style="contentStyle" ref="content"/>
      </div>
    </div>
    <section class="section">
      <div class="section-body">
        <h2 class="section-title">Questions</h2>
        <multi-choice-single v-if="subItemConfig.type === ITEM_TYPE_MULTI_CHOICE_SINGLE"
                             v-for="(_, i) in form.children"
                             :title="`QUESTION NUMBER ${i + 1}`"
                             :key="`sub-item-${i}`" :config="subItemConfig" v-model="form.children[i]"
                             @onClose="removeChildren(i)" />
        <button class="btn btn-light btn-block" @click="addSubItem"><i class="fa fa-plus"></i> Add Question</button>
      </div>
    </section>
  </div>
</template>

<script>
  import { Editor, EditorMenuBar, EditorContent } from 'tiptap'
  import {
    Blockquote,
    HardBreak,
    Heading,
    HorizontalRule,
    OrderedList,
    BulletList,
    ListItem,
    TodoItem,
    TodoList,
    Bold,
    Italic,
    Link,
    Strike,
    Underline,
    History,
  } from 'tiptap-extensions'
  import {ITEM_TYPE_BUNDLE, ITEM_TYPE_MULTI_CHOICE_SINGLE} from "../../const";
  import MultiChoiceSingle from "./MultiChoiceSingle";
  import HasExtraConfigMixin from "../../mixins/HasExtraConfigMixin";

  export default {
    name: "QuizBuilderBundle",
    mixins: [HasExtraConfigMixin],
    props: {
      title: {
        type: String,
        default: 'Bundle Form'
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
    components: { EditorMenuBar, EditorContent, MultiChoiceSingle },
    data : () => ({
      editor: null,
      form: {
        type: ITEM_TYPE_BUNDLE,
        attachment: undefined,
        answer_order_random: true,
        duration: 0,
        content: '<p></p>',
        children: []
      }
    }),
    computed: {
      itemCount: function () {
        return _.get(this.config, 'item_count', 0)
      },
      subItemConfig: function () {
        return _.get(this.config, 'sub-item', {
          type: ITEM_TYPE_MULTI_CHOICE_SINGLE,
          answer_order_random: true,
        })
      }
    },
    watch: {
      form: {
        handler: function (newValue) {
          this.$emit('input', newValue)
        },
        deep: true,
      },
    },
    methods: {
      addSubItem () {
        this.form.children.push({ order: this.form.children.length })
      },
      removeChildren (index) {
        const children = [...this.form.children];
        children.splice(index, 1);
        children.map((c, i) => c.order = i);
        this.form.children = [];
        this.$nextTick(function () {
          this.form.children = children
        })
      }
    },
    created () {
      this.form = Object.assign({}, this.form, this.value, _.pick(this.config, ['answer_order_random', 'duration']))
    },
    mounted () {
      this.editor = new Editor({
        extensions: [
          new Blockquote(),
          new BulletList(),
          new HardBreak(),
          new Heading({ levels: [1, 2, 3] }),
          new HorizontalRule(),
          new ListItem(),
          new OrderedList(),
          new TodoItem(),
          new TodoList(),
          new Link(),
          new Bold(),
          new Italic(),
          new Strike(),
          new Underline(),
          new History(),
        ],
        onUpdate: ({ getHTML }) => {
          this.form.content = getHTML()
        },
      });

      this.editor.setContent(this.form.content?.replaceAll(' ', '&nbsp;'))

      this.$nextTick(() => {
        if (this.value.children.length === 0) {
          for (let i = 0; i < this.itemCount; i++)
            this.addSubItem();
        } else {
          // manually trigger event. due a bug cannot read change in HasExtraConfigMixin
          this.lineCount()
        }
      })
    },
    beforeDestroy () {
      this.editor.destroy()
    }
  }
</script>

<style scoped>

</style>
