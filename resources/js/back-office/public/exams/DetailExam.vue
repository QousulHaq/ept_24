<template>
  <div v-if="isReady">
    <div class="card" v-if="! noHeader">
      <div class="card-body">
        <div class="float-left">
          <button @click.prevent="redirect('/back-office/monitor')" class="btn btn-info"><i class="fa fa-list"></i>
            &nbsp; List
          </button>
        </div>
        <div class="float-right">
          <button v-if="! inFuture" class="btn btn-default btn-sm" disabled>Scheduled</button>
          <button v-else-if="! exam.started_at" class="btn btn-primary btn-sm" @click.prevent="startAnExam">
            Start Exam
          </button>
          <button v-else class="btn btn-danger btn-sm" @click.prevent="endExamNow">End Exam Now</button>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
            <h4>Name:</h4>
            <span class="text-uppercase font-weight-bold text-danger">{{ exam.name }}</span>
          </div>
          <div class="col-md-3" style="cursor: pointer" @click="editSchedule">
            <h4>Scheduled:</h4>
            <span>{{ dashIfNull(dateFormat(exam.scheduled_at)) }}</span>
          </div>
          <div class="col-md-3">
            <h4>Ended:</h4>
            <span>{{ dashIfNull(dateFormat(exam.ended_at)) }}</span>
          </div>
          <div class="col-md-3">
            <h4>Started:</h4>
            <span>{{ (exam.started_at !== null) ? dateFormat(exam.started_at) : 'Not Yet' }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="card" v-if="exam.package['is_encrypted']">
      <div class="card-body">
        <div class="alert alert-dark">This exam is using encrypted package! [{{ exam.package.title }}]</div>
        <div class="alert alert-danger" v-if="! exam.package['distribution_options']['encryptor_ready']">
          Encryptor unavailable, <a href="#" @click="initEncryptor">click here to init encryptor.</a>
        </div>
      </div>
    </div>

    <h1 style="color: #34395e;font-size: 24px;font-weight: 700;padding: 20px">Participants</h1>

    <div class="row px-2">
      <div class="col-md-4 col-lg-3" v-for="user in exam.participants">
        <div class="card" :key="`${user.id}`">
          <div class="card-body table-responsive">
            <img :src="checkImage(user.attachments[0])" alt="Image" class="img-fluid" width="100%">
            <div class="d-flex mt-5 justify-content-between">
              <div class="d-inline">
                <h4 class="d-inline">{{ user.name }}</h4>
                <b v-if="findParticipant(user.hash)"><h4 class="text-success d-inline">&bull;</h4> Online</b>
                <span v-else><i class="text-secondary d-inline">&bull;</i> Offline</span>
              </div>
            </div>

            <span>Status: {{
                (user.detail.status === 'banned') ? 'Disqualified'.toUpperCase() : user.detail.status.toUpperCase()
              }}</span>

            <button v-if="user.detail.status !== 'banned'" class="btn btn-danger btn-block my-1"
                    @click.prevent="disqualifiedParticipant(user.hash)">Disqualified
            </button>
            <button v-else class="btn btn-success btn-block my-1" @click.prevent="qualifiedParticipant(user.hash)">
              Qualified
            </button>
          </div>
          <div class="card-footer event-log">
            <h6>Logs :</h6>
            <ul class="">
              <li class="list-unstyled" v-for="log in user.detail.logs">
                {{ log.content }}
                <small class="time text-primary">{{ log.diff_time }}</small>
              </li>
              <!--              <li class="list-unstyled">-->
              <!--                Connected-->
              <!--                <small class="time text-primary">3 Min Ago</small>-->
              <!--              </li>-->
              <!--              <li class="list-unstyled">-->
              <!--                Disconnected-->
              <!--                <small class="time text-primary">4 Min Ago</small>-->
              <!--              </li>-->
              <!--              <li class="list-unstyled">-->
              <!--                Open other window-->
              <!--                <small class="time text-primary">4 Min Ago</small>-->
              <!--              </li>-->
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {differenceInSeconds} from 'date-fns'

export default {
  name: "DetailExam",
  props: {
    noHeader: {
      type: Boolean,
      default: false
    },
    examId: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      exam: {},
      participants: new Set(),
      inFuture: false,
      state: {
        with: ['participants', 'participants.attachments', 'package']
      }
    }
  },
  computed: {
    isReady: function () {
      return this.exam.hasOwnProperty('started_at')
    },
    isEncrypted: function () {
      return this.exam.package.is_encrypted
    }
  },
  created() {
    this.getExam()
    if (this.exam.started_at !== null) {
      this.getListen()
    }

    setInterval(() => {
      this.inFuture = this.isReady && differenceInSeconds(new Date(), new Date(this.exam.scheduled_at)) > 0
    }, 1000)
  },
  methods: {
    startAnExam() {
      this.request('api.back-office.exam.start-exam',
        {'exam': this.examId}).then(() => {
        this.swal('Success', 'Exam was started!', 'success').finally(() => {
          this.getExam()
          this.getListen()
          this.$forceUpdate()
        })
      })
    },
    endExamNow() {
      this.swal({
        title: 'Are you sure to end this exam now?',
        text: 'This action cannot be revert!',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then(result => {
        if (result) {
          this.request('api.back-office.exam.end-exam',
            {'exam': this.examId}).then(() => {
            this.swal('Success', 'Exam was ended, you might need to wait for a minutes before exam appear on history page!', 'success').finally(() => {
              this.redirect('/back-office/monitor')
            })
          })
        }
      })
    },
    disqualifiedParticipant(user) {
      this.swal({
        title: 'Are you sure to disqualified this participant?',
        text: 'Reverting this action may causing unexpected incident !',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((result) => {
        if (result) {
          this.request('api.back-office.exam.disqualified-participant',
            {'exam': this.examId, 'user': user}).then(() => {
            this.swal('Success', 'Participant was disqualified!', 'success').finally(() => {
              this.getExam()

              this.$forceUpdate()
            })
          })
        }
      })
    },
    qualifiedParticipant(user) {
      this.swal({
        title: 'Are you sure to qualified this participant ?',
        text: 'Reverting this action may causing unexpected incident !',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((result) => {
        if (result) {
          this.request('api.back-office.exam.qualified-participant',
            {'exam': this.examId, 'user': user}).then(() => {
            this.swal('Success', 'Participant was qualified!', 'success').finally(() => {
              this.getExam()

              this.$forceUpdate()
            })
          })
        }
      })
    },
    getListen() {
      this.$echo.join('exam.' + this.examId)
        .here((response) => {
          response.forEach(e => {
            this.participants.add(e.hash)
          })

          this.$forceUpdate()
        })
        .joining((response) => {
          this.participants.add(response.hash)
          this.sendLog(response.hash, 'connected to server!', ['connection', 'connected'])

          this.$forceUpdate()
        })
        .leaving((response) => {
          this.participants.delete(response.hash)
          this.sendLog(response.hash, 'disconnected from server!', ['connection', 'disconnected'])

          this.$forceUpdate()
        })
        .listen('Exam\\Participant\\ParticipantReady', response => {
          this.sendLog(response.hash, 'enter room', ['state', 'ready'])

          this.$forceUpdate()
        })
        .listenForWhisper('security', (kind) => {
          switch (kind.type) {
            case 'mouseleave':
              this.sendLog(kind.hash, 'possible open another window!', ['security', 'mouseleave'])
              break;
          }
        })
    },
    getParticipant(hash) {
      return this.exam.participants.find(p => p.hash === hash)
    },
    sendLog(userHash, content, tags = []) {
      if (userHash) {
        return
      }

      this.request('api.back-office.exam.participant.log', {
        exam: this.exam.id,
        user: userHash,
        content,
        tags,
      }, {}, false).then(() => this.getExam(false))
    },
    editSchedule() {

    },
    getExam(withLoading = true) {
      this.request('api.back-office.exam.show', {exam: this.examId}, {
        params: this.state
      }, withLoading)
        .then(response => {
          this.exam = response.data
        })
    },
    initEncryptor() {
      if (this.exam.package['distribution_options']['has_passphrase']) {

      } else {
        this.request('api.back-office.exam.decrypt', {exam: this.examId}).then(() => this.reload())
      }
    },
    findParticipant(hash) {
      return this.participants.has(hash)
    }
  }
}
</script>

<style>
.event-log {
  max-height: 200px;
  overflow-y: auto;
}
</style>
