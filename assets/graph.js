var base_url = d3.select('body').attr('data-ajax-base'),
  w = document.getElementById('vis').offsetWidth,
  h = w/2,
  margin = 20;
  
function getParams() {
  return {
    'max-points': w-margin*1.5,
    'period': d3.select('#period>button.active').attr('data-period')
  }
}

function fetch() {
  w = document.getElementById('vis').offsetWidth;
  h = w/2;
  // not using the josn helper function as it's error variable is empty
  getJson("data", getParams(), function(error, data) {
    if(error) {
      d3.select('#vis').html("<div class='alert'>Problem loading data: "+error+"</div>");
    }
    else {
      var tmpSelection = selected
      draw( loadDates( data ));
      if(tmpSelection) select(tmpSelection);
    }
  });
}

fetch();

function loadDates(data) {
  for(var i=0;i<data.points.length;i++) {
    data.points[i].time = new Date(data.points[i].time);
  }
  data.min.x = new Date(data.min.x);
  data.max.x = new Date(data.max.x);
  return data;
}

var selected, vis;

function draw(data) {
  var x_tick_count = 7,
    min = data.min,
    max = data.max,
    y = d3.scale.linear().domain([max.y, min.y]).range([0 + margin/2, h - margin]),
    x = d3.time.scale().domain([min.x, max.x]).range([0 + margin, w - margin/2]);

  selected = false;
  vis = d3.select("#vis")
    .html("")
    .append("svg:svg")
    .attr("width", w)
    .attr("height", h)
    .append("svg:g");
    
  var colors = ["blue", "green", "red", "orchid", "hsl"];
  var color_value = 0;
  var color_map = {};
  
  for(var key in data.points[0]) {
    if("time" === key) continue;
    var line = d3.svg.line()
      .x(function(d,i) { return x(d.time); })
      .y(function(d) {
        if(key in d) return y(d[key]);
        return y(0);
      });

console.log(key);
    color_map[key] = colors[color_value++];
    if(color_value>=colors.length) color_value = 0;
    
    if(!(key in data.legend)) {
      data.legend[key] = {id:key,text:key};
    }
    
//    console.log(key, color_map, data.legend);
    vis.append("svg:path")
      .attr("d", line(data.points))
      .attr("class", color_map[key] + " " + data.legend[key].id);
  }
  

  
  d3.select('#legend').html("")
    .selectAll('div')
    .data(function() {
      return Object.keys(data.legend)
        .map(function(key) {
          return {
            'key': key,
            'id': this[key].id,
            'long': this[key].text
          }
        }, data.legend);
    })
    .enter().append('a')
    .attr('class', function(s) { return color_map[s.key] + " label"; })
    .attr('data-id', function(s) { return s.id })
    .text(function(s) { return s.long; })
    .on('click', function() {
      var id = d3.select(this).attr('data-id');
      select(id);
    });

  // X axis
  vis.append("svg:line")
      .attr("x1", x(min.x))
      .attr("y1", y(min.y))
      .attr("x2", x(max.x))
      .attr("y2", y(min.y));
  
  // Y axis
  vis.append("svg:line")
      .attr("x1", x(min.x))
      .attr("y1", y(min.y))
      .attr("x2", x(min.x))
      .attr("y2", y(max.y));
  
  vis.selectAll(".xLabel")
      .data(x.ticks(x_tick_count))
      .enter().append("svg:text")
      .attr("class", "xLabel")
      .text(x.tickFormat(x_tick_count))
      .attr("x", function(d) { return x(d) })
      .attr("y", h)
      .attr("text-anchor", "middle")
  
  vis.selectAll(".yLabel")
      .data(y.ticks(4))
      .enter().append("svg:text")
      .attr("class", "yLabel")
      .text(String)
      .attr("x", 0)
      .attr("y", function(d) { return y(d) })
      .attr("text-anchor", "right")
      .attr("dy", 4)
  
  vis.selectAll(".xTicks")
      .data(x.ticks(x_tick_count))
      .enter().append("svg:line")
      .attr("class", "xTicks")
      .attr("x1", function(d) { return x(d); })
      .attr("y1", y(min.y))
      .attr("x2", function(d) { return x(d); })
      .attr("y2", y(min.y)+3)
  
  vis.selectAll(".yTicks")
      .data(y.ticks(4))
      .enter().append("svg:line")
      .attr("class", "yTicks")
      .attr("y1", function(d) { return y(d); })
      .attr("x1", x(min.x)-3)
      .attr("y2", function(d) { return y(d); })
      .attr("x2", x(min.x))
      
  d3.selectAll('.controls')
    .classed('hidden', false)
    .selectAll('#period>button')
    .on('click', function() {
      d3.selectAll('#period>button').classed('active', false);
      var btn = d3.select(this).classed('active', true);
      fetch();
    });
}

function getJson(url, data, callback) {
  if("function" == typeof data) {
    callback = data;
    data = {};
  }
  var url = base_url + url + queryString(data);
  
  d3.xhr(url, function(error, request) {
    var data;
    if(error) {
      error = error.status + ": " + error.statusText;
    }
    else {
      if("application/json" === request.getResponseHeader('Content-Type')) {
        data = JSON.parse(request.responseText);
        if(data.error)
          error = data.error;
      }
      else {
        error = "Unkown Content-Type: "+request.getResponseHeader('Content-Type');
      }
    }
    callback(error, data);
  });
  
  function queryString(data) {
    var params = Object.keys(data).map(function(key) {
      return encodeURIComponent(key) + "=" + encodeURIComponent(data[key]);
    });
    if(params.length) return "?" + params.join("&");
    return '';
  }
}

function select(selection) {
  var paths = vis.selectAll('path')
    
  if(selected == selection) {
    paths.classed('hidden', false);
    selected = false;
    return;
  }
  paths
    .classed('hidden', true)
    .filter('.'+selection)
    .classed('hidden', false)
  selected = selection;
}