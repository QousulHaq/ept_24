<template>
  <div v-if="item !== null">
    <a-row type="flex" justify="end">
      <a-col :md="item.label.length > 4 ? 5 : 4" :xs="6">
        <a-tooltip placement="top" style="display: block">
          <template slot="title">
            <span>current number</span>
          </template>
          <a-button disabled block style="border-color: firebrick"><b>{{item.label}}</b></a-button>
        </a-tooltip>
      </a-col>
      <a-col :md="2" :xs="6">
        <a-tooltip placement="top" style="display: block">
          <template slot="title">
            <span>time</span>
          </template>
          <a-button disabled block>
            <countdown :available-time="countdown.availableTime"
                       :freeze="countdown.freeze"
                       ref="countdown"
                       :gap="countdown.gap" @timeout="timeout" @every="every"/>
          </a-button>
        </a-tooltip>
      </a-col>
      <a-col :md="2" :xs="6">
        <a-button @click="showDrawer" block>
          <a-icon type="profile"/>
        </a-button>
      </a-col>
    </a-row>
    <item-navigation :visible="visible" @close="onClose"/>
    <a-layout class="layout" :style="{ margin: '2.4em 0' }">
      <a-layout-content>
        <div :style="{ background: '#fff', padding: '24px', minHeight: '280px' }">
          <quiz-builder v-if="item !== null" v-show="isConnected" :item="item" :value="attempt['answer']" :disabled="disabled"
                        @change="processChangeAnswer"
                        @onCountdownFreezeChange="requestChangeFreeze"/>
          <div v-show="! isConnected">
            <b>currently you're not connected to the server. </b>
            the question will appear while you connected to server !
          </div>
        </div>
      </a-layout-content>
    </a-layout>
  </div>
</template>

<script>
  import { mapGetters, mapActions, mapMutations, mapState } from 'vuex'
  import ItemNavigation from '../../components/perform/ItemNavigation'
  import QuizBuilder from '../../components/perform/QuizBuilder'
  import Countdown from '../../components/perform/Countdown'
  import { MUTATION as PERFORM_MUTATION } from '../../store/modules/exam/perform'

  export default {
    name: "PerformTackle",
    components: { ItemNavigation, QuizBuilder, Countdown },
    data: () => ({
      visible: false,
      disabled: false,
      countdown: {
        gap: 5,
        freeze: false,
        lock: false,
        watchingId: null,
        availableTime: Infinity
      }
    }),
    computed: {
      ...mapState('auth', {
        isConnected: state => state.connection_state === 'connected'
      }),
      ...mapGetters('exam/perform', {
        section: 'activeSection',
        item: 'activeItem',
        attempt: 'activeAttempt',
        itemDuration: 'itemDuration'
      }),
    },
    watch: {
      isConnected: function () {
        this.processIsConnected()
      },
      section: function (newSection) {
        if (! this.itemDuration) this.countdownParamsProcess(newSection)
      },
      item: function (newItem) {
        if (this.itemDuration) this.countdownParamsProcess(newItem)
      }
    },
    methods: {
      ...mapMutations('exam/perform', {
        changeActive: PERFORM_MUTATION.CHANGE_ACTIVE
      }),
      ...mapActions('exam/perform', {
        saveAnswer: 'saveAnswer',
        saveTime: 'saveTime',
        nextItemOrSection: 'next'
      }),
      processIsConnected () {
        if (! this.isConnected) {
          if (this.countdown.freeze) {
            this.countdown.lock = true
          } else {
            this.requestChangeFreeze(true)
          }
        } else {
          if (this.countdown.lock) {
            this.countdown.lock = false
          } else {
            this.requestChangeFreeze(false)
          }
        }
      },
      processChangeAnswer ({ value, itemId = null }) {
        switch (this.item.type) {
          case 'multi_choice_single':
          case 'bundle':
            this.saveAnswer({ itemAnswerId: value, itemId })
            break;
        }
      },
      showDrawer () {
        this.visible = true;
      },
      onClose () {
        this.visible = false;
      },
      requestChangeFreeze (value) {
        this.countdown.freeze = value
      },
      countdownParamsProcess ({ id = null, remaining_time = null }) {
        if (id === null || remaining_time === null) {
          console.warn(
            `countdownParamsProcess : id or remaining_time is null\n id = ${id}\n remaining_time ${remaining_time}`)
          return null
        }

        this.$nextTick(function () {
          const needResetCountdown = this.countdown.watchingId !== id
          this.countdown.watchingId = id
          this.countdown.availableTime = remaining_time

          if (needResetCountdown) {
            if (this.$refs.hasOwnProperty('countdown')) this.$refs.countdown.reset()
            if (! this.itemDuration)
              this.requestChangeFreeze(false)
          }
        })
      },
      every () {
        this.saveTime({ gap: this.countdown.gap })
      },
      timeout () {
        this.disabled = true
        this.saveTime({ gap: this.countdown.gap }).then(() => {
          this.nextItemOrSection().then(() => this.disabled = false)
        })
      },
      finishCurrentSection (confirm) {
        if (confirm) {
          if (0 < this.countdown.availableTime && this.countdown.availableTime < Infinity) {
            const key = 'finishing-section'
            this.$message.loading({ content: 'Please wait ⏲...', key })
            this.saveTime({ gap: this.countdown.availableTime + 10 }).then(() => {
              this.nextItemOrSection()
              this.$message.success({ content: 'Okay 👋', key })
            })
          }
        } else {
          this.$message.info('keep going 😃!');
        }
      }
    },
    created () {
      this.processIsConnected()
    },
    mounted () {
      const tickAble = this.itemDuration ? this.item : this.section
      if (tickAble) this.countdownParamsProcess(tickAble)

      setTimeout(() => {
        document.querySelector('body > section').scroll({ top: this.$el?.offsetTop ?? 0, left: 0, behavior: 'smooth' })
      }, 1000)
    }
  }
</script>

<style scoped>

</style>
