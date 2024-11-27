<template>
  <a-drawer
    v-if="activeSection !== null && activeItem !== null"
    placement="right"
    :closable="false"
    @close="close"
    :visible="visible">
    <a-collapse :accordion="true" :activeKey="activeSection.id"
                expandIconPosition="right" :bordered="false">
      <a-collapse-panel v-for="section in sections" :header="section.config.title"
                        :key="section.id" :disabled="section.id !== activeSection.id">
        <a-row type="flex" justify="space-between">
          <a-col v-for="item in section.items" :key="item.id" :md="item.label.length > 4 ? 24 : 11" :sm="24">
            <a-button block @click="changeActiveItem(item.id)" :class="getAnsweredClass(item)"
                      :disabled="(itemDuration && item.id !== activeItem.id) || (section.id !== activeSection.id)"
                      :type="getTypeButton(item)">
              {{item.label}} <span v-if="getAnsweredClass(item).length > 0">&nbsp;<a-icon type="check-circle" /></span>
            </a-button>
          </a-col>
        </a-row>
      </a-collapse-panel>
    </a-collapse>
  </a-drawer>
</template>

<script>
  import { mapGetters, mapMutations } from 'vuex'
  import { MUTATION as PERFORM_MUTATION } from '../../store/modules/exam/perform'

  export default {
    name: "ItemNavigation",
    props: {
      visible: {
        required: true
      }
    },
    computed: mapGetters('exam/perform', {
      sections: 'sections',
      activeSection: 'activeSection',
      activeItem: 'activeItem',
      itemDuration: 'itemDuration',
      sectionItemsAnswered: 'sectionItemsAnswered',
    }),
    watch: {
      activeSection: function () {
        this.close()
      },
      activeItem: function () {
        this.close()
      }
    },
    methods: {
      ...mapMutations('exam/perform', {
        changeActive: PERFORM_MUTATION.CHANGE_ACTIVE
      }),
      getAnsweredClass (item) {
        if (item !== null && this.sectionItemsAnswered.findIndex(id => item.id === id) !== -1) {
          return ['answered']
        }
        return []
      },
      getTypeButton (item) {
        return item.id !== this.activeItem.id ? 'default' : 'primary'
      },
      changeActiveItem (itemId) {
        if (! this.itemDuration)
          this.changeActive({ item: itemId })
      },
      close () {
        this.$emit('close')
      }
    }
  }
</script>

<!--suppress CssUnusedSymbol -->
<style scoped>
  .ant-col {
    margin: 0.25em auto
  }

  .ant-col-md-24 > button {
    height: auto;
    white-space: normal;
    font-size: unset;
  }

  .answered {
    border-color: cornflowerblue;
  }
</style>
