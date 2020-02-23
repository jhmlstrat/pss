<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="The JHML is a face-to-face Strat-o-Matic league, based in the state of Maryland.">
  <meta name="author" content="">
  <meta name="keywords" content="JHML, Strat-o-Matic, Stratomatic, Maryland">

  <title>JHML</title>
  <link rel="icon" href="favicon.ico">
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/ie10-viewport-bug-workaround.css" rel="stylesheet">   <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
  <link href="/css/site.css" rel="stylesheet">
  <link href="pss.css" rel="stylesheet">
  <link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap/dist/css/bootstrap.min.css"/>
  <link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.css"/>
  <script src="https://unpkg.com/vue/dist/vue.js" type="text/javascript"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <script src="https://unpkg.com/babel-polyfill/dist/polyfill.min.js"></script>
  <script src="https://unpkg.com/bootstrap-vue/dist/bootstrap-vue.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.9.0/Sortable.min.js"></script>
<!--
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/15.0.0/vuedraggable.min.js" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/sortablejs/Sortable.min.js"></script>
  <script src="https://unpkg.com/vue-draggable/lib/vue-draggable.min.js"></script>
  <script src="https://unpkg.com/vue-draggable/polyfills/index.js"></script>
-->
  <script src="https://unpkg.com/lodash/lodash.min.js"></script>
  <script src="menuComponent.vue" type="text/javascript"></script>
  <script src="rosterComponent.vue" type="text/javascript"></script>
  <script src="rotationComponent.vue" type="text/javascript"></script>
  <script src="scheduleComponent.vue" type="text/javascript"></script>
</head>

<body background="../gif/paper1.jpg">
  <div id="myPss">
    <component :is="currentComponent"><component>  
  </div>

  <script>
    const eBus = new Vue();

//    Vue.use(VueMq, {
//      breakpoints: { // default breakpoints - customize this
//        sm: 450,
//        md: 1250,
//        lg: Infinity,
//      },
//          defaultBreakpoint: 'sm' // customize this for SSR
//    });

    var vue = new Vue({
      el: "#myPss",
      data: {
        currentComponent: "menuComponent",
        teamname: '',
        year: undefined,
        config: {},
        roster: {},
        rotation: {},
        schedule: {},
        game: 0,
        injury: false,
        gameInProgress: false,
      },
      components: {
        menuComponent: MenuComponent,
        rosterComponent: RosterComponent,
        scheduleComponent: ScheduleComponent,
        rotationComponent: RotationComponent,
      },
      mounted: function() {
        if ("<?php echo $_GET['teamname']?>" !=  undefined) {
          this.setTeamName("<?php echo $_GET['teamname']?>");
        } else {
          this.setTeamName("guest");
        }
        this.loadConfig();
      },
      watch: {
        year: function() { if (this.teamname == 'guest') return; this.loadRoster(); this.loadSchedule(); this.loadRotation();},
      },
      methods: {
        setTeamName(tn) {
          //console.log(tn);
          this.teamname = tn;
          this.emitData();
        },
        loadConfig() {
          var self = this;
          let headers = {headers:{'X-Authorization':'TooManyMLs'}};
          axios.get('/pss/api/getConfig.php',headers)
          .then(function (response) {
            self.config = response.data;
            ////console.log(JSON.stringify(self.config));
            if (self.year == undefined) {
              self.year = self.config.current_year;
              self.emitData();
            } else self.year = self.year;
            //self.loadRoster();
            //self.loadSchedule();
          })
          .catch(function (error) {
            console.error(error);
          });
        },
        loadRoster() {
          var self = this;
          let headers = {headers:{'X-Authorization':'TooManyMLs'}};
          axios.get('/pss/api/getRoster.php?team='+this.teamname+'&year='+this.year,headers)
          .then(function (response) {
            self.roster = response.data;
            //console.log(JSON.stringify(self.roster));
            self.emitData();
          })
          .catch(function (error) {
            console.error(error);
          });
        },
        loadRotation() {
          var self = this;
          let headers = {headers:{'X-Authorization':'TooManyMLs'}};
          axios.get('/pss/api/getRotation.php?team='+this.teamname+'&year='+this.year,headers)
          .then(function (response) {
            self.rotation = response.data;
            //console.log(JSON.stringify(self.rotation));
            self.emitData();
          })
          .catch(function (error) {
            console.error(error);
          });
        },
        loadSchedule() {
          var self = this;
          let headers = {headers:{'X-Authorization':'TooManyMLs'}};
          axios.get('/pss/api/getSchedule.php?team='+this.teamname+'&year='+this.year,headers)
          .then(function (response) {
            self.schedule = response.data;
            //console.log(JSON.stringify(self.schedule));
            self.emitData();
          })
          .catch(function (error) {
            console.error(error);
          });
        },
        emitData() {
          eBus.$emit('configUpdated',this.config);
          eBus.$emit('teamnameUpdated',this.teamname);
          eBus.$emit('rosterUpdated',this.roster);
          eBus.$emit('rotationUpdated',this.rotation);
          eBus.$emit('scheduleUpdated',this.schedule);
        },
      },
    });

  </script>
</body>
</html>
