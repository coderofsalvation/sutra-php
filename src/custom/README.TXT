Custom folder 
============= 
Because you never want to refuse a feature

What is it?  
~~~~~~~~~~~ 
In this folder, all exotic/projectspecific implementation is done.

Why?  
~~~~ 
By separating custom implementation from generic implementation, one
can easily update generic implementations.  Also the robustness and testing
cases are more reliable, and will have a longer lifecycle.  Whenever something
malfunctions, the exotic/projectspecific implementation can easily be
'unplugged' (by removing custom files)

Example?  
~~~~~~~~ 
Ok, lets assume you made 4 sites with sutra, site A,B,C,D.
Now...you have created module X..a very succesfull module which can help your
client to achieve world domination.  Client A,B & C are very happy, but client
D wants a custom feature.  Here you are in dilemma, because if you alter module
X for client D, you will have two versions of module X :( So, instead of
altering the code of module X (and risking/loosing your testcases of the past),
you put your custom feature implementation in the custom folder. Since it will just
sit there and listen for events, it will be unobtrusive...now the
core implementation of the module remains stable, and some clients have there custom features.

So its a module of a module?  
~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 
Yes, if you want to put it like that..yes.  Or a plugin of a plugin, or etc.. :)

Always say NO to features 
~~~~~~~~~~~~~~~~~~~~~~~~~ 
Lots of gurus and experts do
advice this.  But the sutra perspective is always to help people.  With this
custom folder, you are always able to do exotic (and sometimes stupid) feature
requests without dangering the implementation of a well-tested module.

Well, this is all nice but..I prefer to edit in modules
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
That's still possible ofcoarse, do what you like :)
But remember, if you are making something for a client : he'll respect you more
if, whenever a bug occurs, you can 'fallback' to the basic functionality.

