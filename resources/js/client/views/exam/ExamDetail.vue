<template>
  <div v-if="exam.package">
    <a-card>
      <a-row type="flex" align="middle">
        <a-col :sm="6" :md="4"><h3>NAME</h3></a-col>
        <a-col :sm="6" :md="4">{{exam.name}}</a-col>
      </a-row>
      <a-row type="flex" align="middle">
        <a-col :sm="6" :md="4"><h3>CODE</h3></a-col>
        <a-col :sm="6" :md="4"><a-tag color="red">{{exam.package.code}}</a-tag></a-col>
      </a-row>
      <a-row type="flex" align="middle">
        <a-col :sm="6" :md="4"><h3>STATUS</h3></a-col>
        <a-col :sm="6" :md="4"><a-tag color="blue">{{exam.detail.status.toUpperCase()}}</a-tag></a-col>
      </a-row>
      <a-row type="flex" align="middle">
        <a-col :sm="6" :md="4"><h3>SCHEDULED AT</h3></a-col>
        <a-col :sm="6" :md="4">{{dateFormat(exam.scheduled_at).toUpperCase()}}</a-col>
      </a-row>
      <a-row type="flex" align="middle" v-if="exam.ended_at">
        <a-col :sm="6" :md="4"><h3>ENDED AT</h3></a-col>
        <a-col :sm="6" :md="4">{{dateFormat(exam.ended_at).toUpperCase()}}</a-col>
      </a-row>
    </a-card>
    <a-card :style="{ marginTop: '20px' }">
      <a-row type="flex" justify="space-between">
        <a-col :sm="6" :md="4">
          <a-button @click="$router.back()" type="primary" block><a-icon type="left"/> BACK</a-button>
        </a-col>
        <a-col :sm="6" :md="4">
          <a-button @click="enterExam" v-if="readyToEnter && isFullscreen" type="primary" block>Click Here to Start <a-icon type="right"/></a-button>
          <a-button @click="toFullscreen" v-else-if="readyToEnter" type="primary" block>Fullscreen to Start</a-button>
        </a-col>
      </a-row>
    </a-card>
  </div>
</template>

<script>
  import { formatRelative, differenceInSeconds } from 'date-fns'
  import { mapActions } from 'vuex'

  export default {
    name: "ExamDetail",
    data() {
      return {
        isFullscreen: false,
      }
    },
    computed: {
      exam: function () {
        return this.$store.getters['exam/getExamById'] (this.$route.params.id) ?? {}
      },
      readyToEnter: function () {
        return (! ['not ready', 'banned', 'finished'].includes(this.exam.detail.status))
          && differenceInSeconds(new Date(), new Date(this.exam.scheduled_at)) > 0
      },
      appEl: function () {
        return document.getElementsByTagName('body')[0]
      }
    },
    methods: {
      ...mapActions('exam', {
        enroll: 'enroll'
      }),
      dateFormat(time) {
        return formatRelative(new Date(time), new Date())
      },
      enterExam () {
        this.enroll(this.$route.params.id).then(_ => {
          this.$router.push({ name: 'perform.index' })
        })
      },
      toFullscreen() {
        this.appEl.requestFullscreen()
      },
    },
    created () {
      if (! this.exam)
        this.$router.push({ name: 'exam.list' })
    },
    mounted() {
      this.appEl.addEventListener('fullscreenchange', () => {
        if (document.fullscreenElement) {
          this.isFullscreen = true
        } else {
          this.isFullscreen = false
          setTimeout(() => this.appEl.requestFullscreen(), 5000)
        }
      });

      this.isFullscreen = !!document.fullscreenElement
    }
  }
</script>

<style scoped>

</style>
