str = 'spec'
count = 0;

args = [None] + range(-8, 8)
stepArgs = [None] + range(-8, 0) + range(1, 8)

print 'Args,Result'

for i in args:
    for j in args:
        for k in stepArgs:
            start = '' if i == None else i
            stop = '' if j == None else j
            step = '' if k == None else k
            print "[%s:%s:%s],%s" % (start, stop, step, str[i:j:k])
