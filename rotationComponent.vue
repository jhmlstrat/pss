var RotationComponent = {
  name: 'RosterComponent',
  template: `
    <div class='container'>
{{ currentComponent() }}
      <b-row>
        <div class='col-xs-2 text-center'>
          <a role='button' class='btn btn-lg btn-link' href='#' v-on:click='switchToRoster();'>Back</a>
        </div>
        <div class='col-xs-8'>
        </div>
        <div class='col-xs-2 text-center'>
          <a role='button' class='btn btn-lg btn-link'href='/jhmlhome.html'>JHML Home</a>
        </div>
      </b-row>
      <h1><center>{{ team.team_name }}</center></h1>
      <b-row v-for='rotations in chunkedRotation' v-bind:key='rotations[0].game'>
        <b-col class='fixed mt-2' cols='12' sm='6' lg='3' v-for='rot in rotations' v-bind:key='rot.game'>
          <span v-html='pprintGame(rot.game)' />: <b-form-select style='width:80%' v-model='rot.pitcher' v-bind:options='options' v-on:change='calculateStarts()'/>
        </b-col>
      </b-row>
      <b-row class='float-right'>
        <b-col cols='12'>
          <b-button v-on:click='onSubmit' variant='primary' class='mr-4'>Submit</b-button>
          <b-button v-on:click='onReset' variant='danger' class='mr-4'>Reset</b-button>
        </b-col>
      </b-row>
      <br/>
      <b-row>
        <b-col cols='12' lg='8'>
          <b-table striped hover small outlined bordered v-bind:items="starters" v-bind:fields="fields">
            <template slot="innings" slot-scope="data">
              {{ data.item.innings == undefined ? 0 : data.item.innings }}
            </template>
            <template slot="starts" slot-scope="data" class="float-right">
              {{ data.item.starts == undefined ? 0 : data.item.starts }}
            </template>
            <template v-slot:cell(innings_per_start)="data">
              {{ data.item.starts == 0 ? '-' : (data.item.innings / data.item.starts).toFixed(1) }}
            </template>
          </b-table>
        </b-col>
      </b-row>
    </div>
  `,
  data() {
    return {
      team: {},
      teamname: 't',
      config: {},
      roster: {},
      rotation: {},
      starters: [],
      window: {
        width: 0,
        height: 0
      },
      options: [],
      fields: [
        'name',
        {key: 'innings', tdClass: "text-right", thClass: "text-center"},
        {key: 'starts', tdClass: "text-right", thClass: "text-center"},
        {key: 'innings_per_start', tdClass: "text-right", thClass: "text-center"},
      ],
    }
  },
  //created() {
  //  window.addEventListener('resize', this.handleResize)
  //  this.handleResize();
  //},
  //destroyed() {
  //  window.removeEventListener('resize', this.handleResize)
  //},
  watch: {
    config() {
      //console.log(this.config)
      this.setNames();
    },
    teamname() {
      this.setNames();
    },
    roster() {
      if (this.roster == undefined || this.roster == null) return;
      console.log('Here');
      this.options = [{'value': '', text: 'Not Scheduled'}];
      this.starters = [];
      if (this.roster.pitchers) {
        for (b of this.roster.pitchers) {
          if (b.player.endure.includes('S(')) {
            this.options.push({'value':b.player.name,'text':b.player.name});
            this.starters.push({'name':b.player.name,'innings':(Math.trunc(b.player.ip*102/162*1.1*3)/3).toFixed(1),'starts':0});
          }
        }
        this.calculateStarts();
      }
    },
  },
  computed: {
    chunkedRotation() { if (this.rotation == undefined || this.rotation.rotation == undefined) return []; return _.chunk(this.rotation.rotation.rotation,4) },
  },
  mounted() {
    eBus.$on('configUpdated',(c) => { this.config=c; })
    eBus.$on('teamnameUpdated',(tn) => { this.teamname=tn; })
    eBus.$on('rosterUpdated',(r) => { this.roster=r;})
    eBus.$on('rotationUpdated',(r) => { this.rotation=r; this.calculateStarts(); })
    if (this.teamname == 't' && vue != undefined) vue.emitData();
  },
  methods: {
    currentComponent() { return vue.currentComponent; },
    //handleResize() {
    //  this.window.width = window.innerWidth;
    //  this.window.height = window.innerHeight;
    //},
    setNames() {
      if (this.teamname == 't') return;
      if (! this.config.current_year) return;
      for (y of this.config.years) {
        if (y.year == this.config.current_year) {
          for (t of y.teams) {
            if (t.team == this.teamname) this.team = t;
          }
        }
      }
    },
    switchToMenu() {
      vue.currentComponent='menuComponent';
      vue.emitData();
    },
    switchToRoster() {
      vue.currentComponent='rosterComponent';
      vue.emitData();
    },
    pprintGame(g) {
      if (g < 10) return '&nbsp;&nbsp;' + g;
      if (g < 100) return '&nbsp;' + g;
      return g;
    },
    onSubmit(evt) {
      evt.preventDefault();
      var self = this;
      let headers = {headers:{'X-Authorization':'TooManyMLs'}};
      axios.put('/pss/api/putRotation.php',{data: this.rotation},headers)
      .then(function (response) {
        //console.log(response);
        self.switchToRoster();
      })
      .catch(function (error) {
        console.error(error);
        self.switchToRoster();
      });
    },
    onReset(evt) {
      evt.preventDefault();
      vue.loadRotation();
    },
    calculateStarts() {
      for (s of this.starters) s.starts = 0;
      for (p of this.rotation.rotation.rotation) {
        if (p.pitcher == '') continue;
        for (s of this.starters) {
          if (p.pitcher == s.name) {
            s.starts ++;
          }
        }
      }
    }
  },
};

