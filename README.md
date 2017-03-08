<h1>README:</h1>
<img src="https://github.com/aghanathan/FrameMaker/blob/master/Example-Framed-Picture.png"/>

Live Demo at: <a href="http://thinknesia.com/FrameMaker">http://thinknesia.com/FrameMaker</a>

# FrameMaker
Frame your Photo with &lt;? PHP ?> works!

<h2>WaterMarkThis Class</h2>
This class allows to watermark a given image. It requires a watermark image.
The resulting image will be show on the same format as the input/background image. Supported formats: jpeg, jpg, png, gif

<h2>Usage</h2>
<h3>Creating The Object</h3>
<pre><code>$img = new WaterMarkThis('yer_pic.png', 'yer_frame.png');</code></pre>
<h3>Showing the image</h3>
<pre><code>$img->show();</code></pre>
