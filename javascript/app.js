// Set up width and height for the SVG container
var width = 600;
var height = 400;

// Create an SVG element
var svg = d3.select("#map")
    .attr("width", width)
    .attr("height", height);

// Define a projection (you may need to adjust the parameters depending on your data)
var projection = d3.geoMercator()
    .fitSize([width, height], { type: "FeatureCollection", features: data.features }) // Adjust projection to fit data
    .translate([width / 2, height / 2]);

// Create a path generator
var path = d3.geoPath()
    .projection(projection);

// Load GeoJSON data
d3.json("map.json").then(function(data) {
    // Bind data and create path elements
    svg.selectAll("path")
        .data(data.features)
        .enter()
        .append("path")
        .attr("d", path)
        .attr("fill", "steelblue") // Adjust styling as needed
        .attr("stroke", "#fff");
});
