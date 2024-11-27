<template>
  <a-row type="flex" justify="space-between" class="overflow-hidden">
    <a-col :md="11" :sm="24">
      <music-icon v-if="getExtra('audio')" :played="audioContext_played" width="300" height="300"/>
      <editor-content v-if="tiptap.content" class="editor__content" :editor="tiptap.content"
                      ref="content" :style="contentStyle"/>
      <br>
      <span>{{ note }}</span>
    </a-col>
    <a-divider type="vertical" orientation="center" :style="{ height: 'auto' }"/>
    <a-col :md="11" :sm="24" :style="{ overflowY: 'auto', maxHeight: '65vh' }" ref="children-wrapper">
      <div v-for="(subItem, i) in subItems" :key="i">
        <a-divider type="horizontal" :ref="'child-active-'+subItem.id"/>
        <a-button block :type="activeItemId === subItem.id ? 'danger' : 'dashed'"><b>{{subItem.label}}</b></a-button>
        <slot name="single" :item="subItem" :value="resolveValue(subItem)"></slot>
      </div>
      <a-divider type="horizontal"/>
      <arrow-navigation/>
    </a-col>
  </a-row>
</template>

<script>
  import extraMixins from '../../extra/mixins'
  import MusicIcon from './part/MusicIcon'
  import { mapState, mapGetters } from 'vuex'
  import _ from 'lodash'
  import {
    Blockquote, Bold,
    BulletList,
    HardBreak,
    Heading, History,
    HorizontalRule, Italic, Link,
    ListItem,
    OrderedList, Strike,
    TodoItem, TodoList, Underline
  } from "tiptap-extensions";
  import {Editor, EditorContent} from "tiptap";
  import ArrowNavigation from "../perform/ArrowNavigation";

  const extensions = [
    new Blockquote(), new BulletList(), new HardBreak(), new Heading({ levels: [1, 2, 3] }), new HorizontalRule(),
    new ListItem(), new OrderedList(), new TodoItem(), new TodoList(), new Link(), new Bold(),
    new Italic(), new Strike(), new Underline(), new History(),
  ]

  export default {
    name: "Bundle",
    data: () => ({
      tiptap: {
        content: null,
        subContent: null,
      },
    }),
    props: {
      item: {
        required: true,
        type: Object
      },
    },
    computed: {
      ...mapState('exam/perform', {
        activeItemId: state => state.active.item
      }),
      ...mapGetters('exam/perform', {
        section: 'activeSection'
      }),
      groupLabelBy: function () {
        let sign = this.getExtra('group_by')
        if (sign.indexOf(',') !== -1) {
          return sign.split(',')
        }
        return [sign]
      },
      isGroupParent: function () {
        return (label) => this.groupLabelBy.some(sign => _.startsWith(label, sign))
      },
      groupedItems: function () {
        const groupByLabel = this.groupLabelBy
        if (! groupByLabel) {
          console.warn("group by extra is missing in bundle type")
          return []
        }

        const results = []
        const items = this.section.items

        // we will get index of activeItem
        const activeItemIndex = items.findIndex(i => i.id === this.item.id)
        // then loop it from top to bottom
        for (let i = activeItemIndex; i < items.length; i++) {
          if (this.isGroupParent(items[i].label) && i !== activeItemIndex)
            break
          results.push(items[i])
        }
        if (! this.isGroupParent(this.item.label)) {
          // because of active item label is now same with groupByLabel is indicate that active item is pinched
          // we will loop it from bottom to top
          let i = activeItemIndex
          do {
            i--
            results.push(items[i])
          } while (! this.isGroupParent(items[i].label))
        }

        return results.sort((a, b) => a.order - b.order)
      },
      itemParent: function () {
        // itemParent is a head labeled from group by and should be available at grouped items
        return this.groupedItems.find(i => this.isGroupParent(i.label))
      },
      subItems: function () {
        return this.groupedItems.filter(i => ! this.isGroupParent(i.label))
      },
      disablePlugin: function () {
        return this.itemParent?.id !== this.item.id
      },
      note: function () {
        if (! this.subItems || this.subItems.length === 0)
          return ''

        if (this.subItems.length === 1)
          return 'numbers : ' + this.subItems[0].label

        return 'numbers : ' + this.subItems[0].label + ' - ' + this.subItems[this.subItems.length - 1].label
      }
    },
    mixins: [ extraMixins ],
    components: { EditorContent, MusicIcon, ArrowNavigation },
    watch: {
      item: function () {
        this.bootBundle()
      }
    },
    methods: {
      bootBundle () {
        if (this.tiptap.content !== null) {
          this.tiptap.content.setContent(this.item.content?.replaceAll(' ', '&nbsp;'))
        }

        if (this.tiptap.subContent !== null) {
          this.tiptap.subContent.setContent(((! isNaN(this.item.label) ? this.item.label + '. ' : '') + this.item['sub_content'])?.replaceAll(' ', '&nbsp;'))
        } else {
          this.tiptap.subContent = new Editor({
            extensions,
            content: ((! isNaN(this.item.label) ? this.item.label + '. ' : '') + this.item['sub_content'])?.replaceAll(' ', '&nbsp;'),
            editable: false,
          })
        }

        (this.disablePlugin) ? this.resetPluginData() : this.bootPlugins()

        this.scrollToActive()
      },
      resolveValue (item) {
        return _.get(
          _.get(item, 'attempts', []).find(a => _.get(a, 'attempt_number', -1) === _.get(this.section, 'attempts')),
          'answer',
          null
        )
      },
      scrollToActive () {
        let offsetTop = 0
        if (this.$refs['child-active-'+this.activeItemId]) {
          offsetTop = this.$refs['child-active-'+this.activeItemId][0].$el?.offsetTop
        }

        this.$refs['children-wrapper'].$el.scroll({
          top: offsetTop,
          left: 0,
          behavior: 'smooth'
        })
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

      this.bootBundle()
    }
  }
</script>

<style scoped>

</style>
