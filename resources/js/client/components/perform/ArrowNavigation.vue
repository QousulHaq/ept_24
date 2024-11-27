<template>
  <a-row type="flex" v-if="! section.item_duration">
    <a-col :md="4" :xs="6">
      <a-button @click="prev" block :disabled="! hasPrev">
        <a-icon type="left"/>
      </a-button>
    </a-col>
    <a-col :md="4" :xs="6">
      <a-button @click="next" block v-if="hasNext">
        <a-icon type="right"/>
      </a-button>
      <a-tooltip placement="top" v-else>
        <template slot="title">
          <span>Finish</span>
        </template>
        <a-popconfirm
          title="Are you sure want to finish this section now ?"
          ok-text="Yes"
          cancel-text="No"
          @confirm="finishCurrentSection(true)"
          @cancel="finishCurrentSection(false)">
          <a-button block>
            <a-icon type="clock-circle"/>
          </a-button>
        </a-popconfirm>
      </a-tooltip>
    </a-col>
  </a-row>
</template>

<script>
import {mapActions, mapGetters, mapMutations} from 'vuex'
import {MUTATION as PERFORM_MUTATION} from "../../store/modules/exam/perform";

export default {
  name: "ArrowNavigation",
  computed: {
    ...mapGetters('exam/perform', {
      section: 'activeSection',
      item: 'activeItem',
    }),
    activeItemIndex: function () {
      if (!this.section || !this.section.items || this.section.items.length === 0)
        return -1

      return this.section.items.findIndex(i => i.id === this.item.id)
    },
    hasPrev: function () {
      return this.activeItemIndex !== 0
    },
    hasNext: function () {
      if (!this.section || !this.section.items || this.section.items.length === 0)
        return false

      return this.section.items.length - 1 !== this.activeItemIndex
    },
  },
  methods: {
    ...mapMutations('exam/perform', {
      changeActive: PERFORM_MUTATION.CHANGE_ACTIVE
    }),
    ...mapActions('exam/perform', {
      saveTime: 'saveTime',
      nextItemOrSection: 'next'
    }),
    prev() {
      if (this.hasPrev) this.changeActive({item: this.section.items[this.activeItemIndex - 1].id})
    },
    next() {
      if (this.hasNext) this.changeActive({item: this.section.items[this.activeItemIndex + 1].id})
    },
    finishCurrentSection(confirm) {
      if (confirm) {
        const key = 'finishing-section'
        this.$message.loading({content: 'Please wait ⏲...', key})
        this.saveTime({gap: 86400}).then(() => {
          this.nextItemOrSection()
          this.$message.success({content: 'Ok 👋', key})
        })

      } else {
        this.$message.info('keep going 😃!');
      }
    }

  }
}
</script>

<style scoped>

</style>
