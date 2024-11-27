<template>
  <a-row type="flex" justify="space-between">
    <a-col :md="item['sub_content'] !== null ? 14 : 24"
           :sm="24" style="overflow-x: auto">
      <music-icon v-if="! musicIcon.hidden && getExtra('audio')" :played="audioContext_played" :width="musicIcon.width"
                  :height="musicIcon.height"/>
      <line-count v-if="line_count_context" :leap="getExtra('line_count')"
                  :context="line_count_context"/>
      <editor-content v-if="tiptap.content" :class="{'editor__content': true, 'alphabet_counter': getExtra('alphabet_counter_underline')}" :editor="tiptap.content"
                      ref="content" :style="contentStyle"/>
      <a-divider type="horizontal"/>
      <slot v-if="item['sub_content'] === null"/>
      <div v-if="item['sub_content'] === null">
        <a-divider type="horizontal"/>
        <arrow-navigation/>
      </div>
    </a-col>
    <a-divider v-if="item['sub_content'] !== null" type="vertical" orientation="center" :style="{ height: 'auto' }"/>
    <a-col v-if="item['sub_content'] !== null" :md="9" :sm="24">
      <editor-content v-if="tiptap.subContent" class="editor__content" :editor="tiptap.subContent"/>
      <a-divider type="horizontal"/>
      <slot/>
      <div>
        <a-divider type="horizontal"/>
        <arrow-navigation/>
      </div>
    </a-col>
  </a-row>
</template>

<script>
  import extraMixins from '../../extra/mixins'
  import {Editor, EditorContent} from 'tiptap'
  import {
    Blockquote, HardBreak, Heading, HorizontalRule, OrderedList,
    BulletList, ListItem, TodoItem, TodoList, Bold, Italic, Link,
    Strike, Underline, History
  } from 'tiptap-extensions'
  import LineCount from './part/LineCount'
  import MusicIcon from "./part/MusicIcon"
  import ArrowNavigation from "../perform/ArrowNavigation";

  const extensions = [
    new Blockquote(), new BulletList(), new HardBreak(), new Heading({levels: [1, 2, 3, 4, 5]}), new HorizontalRule(),
    new ListItem(), new OrderedList(), new TodoItem(), new TodoList(), new Link(), new Bold(),
    new Italic(), new Strike(), new Underline(), new History(),
  ]

  export default {
    name: "MultiChoiceSingle",
    mixins: [ extraMixins ],
    components: { EditorContent, LineCount, MusicIcon, ArrowNavigation },
    props: {
      item: {
        required: true,
        type: Object
      },
      disablePlugin: {
        type: Boolean,
        default: false
      },
      musicIcon: {
        type: Object,
        default: () => ({
          width: 300,
          height: 200,
          hidden: false
        })
      }
    },
    data: () => ({
      tiptap: {
        content: null,
        subContent: null,
      },
    }),
    watch: {
      item: function () {
        this.bootMultiChoiceSingle()
      },
      disablePlugin: function () {
        if (this.item.type === 'bundle')
          this.bootMultiChoiceSingle()
      }
    },
    methods: {
      bootMultiChoiceSingle () {
        if (this.tiptap.content !== null) {
          this.tiptap.content.setContent(this.item.content?.replaceAll(' ', '&nbsp;'))
        }

        if (this.tiptap.subContent !== null) {
          this.tiptap.subContent.setContent(((!isNaN(this.item.label) ? this.item.label + '. ' : '') + this.item['sub_content'])?.replaceAll(' ', '&nbsp;'))
        } else {
          this.tiptap.subContent = new Editor({
            extensions,
            content: ((!isNaN(this.item.label) ? this.item.label + '. ' : '') + this.item['sub_content'])?.replaceAll(' ', '&nbsp;'),
            editable: false,
          })
        }

        (this.disablePlugin) ? this.resetPluginData() : this.bootPlugins()
      }
    },
    mounted () {
      if (this.item.content !== null) {
        this.tiptap.content = new Editor({
          extensions,
          content: this.item.content?.replaceAll(' ', '&nbsp;'),
          editable: false,
        })
      }

      if (this.item['sub_content'] !== null) {
        this.tiptap.subContent = new Editor({
          extensions,
          content: this.item['sub_content']?.replaceAll(' ', '&nbsp;'),
          editable: false,
        })
      }

      this.bootMultiChoiceSingle()
    }
  }
</script>

<!--suppress CssUnusedSymbol -->
<style scoped>
  /* https://github.com/twbs/bootstrap/blob/4fc701f6aa8b01fb952733b8a4a0b55337760391/scss/_tables.scss#L182 */
  .ant-col {
    padding: 0.1em;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
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
