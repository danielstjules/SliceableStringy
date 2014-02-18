str = 'spec'
count = 0;

args = [None] + range(-8, 8)
stepArgs = [None] + range(-8, 0) + range(1, 8)

for i in args:
    for j in args:
        for k in stepArgs:
            count += 1
            start = '' if i == None else i
            stop = '' if j == None else j
            step = '' if k == None else k
            print "%s,[%s:%s:%s],%s" % (count, start, stop, step, str[i:j:k])
